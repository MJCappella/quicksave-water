<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SalesInvoice;
use App\Models\StockMovement;
use App\Models\Store;
use App\Models\StoreInventory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $selectedStoreId = $request->query('store_id');
        $stores = Store::where('is_active', true)->get();
        $selectedStore = $selectedStoreId ? Store::find($selectedStoreId) : $stores->first();

        // 1. Sales Revenue
        $totalSalesQuery = SalesInvoice::query();
        if ($selectedStore) {
            $totalSalesQuery->where('store_id', $selectedStore->id);
        }
        $totalSalesRevenue = (float) $totalSalesQuery->sum('grand_total');
        $completedOrdersCount = $totalSalesQuery->count();

        // 2. Stock Valuation
        $inventoryQuery = StoreInventory::with('product');
        if ($selectedStore) {
            $inventoryQuery->where('store_id', $selectedStore->id);
        }
        $inventoryItems = $inventoryQuery->get();

        $totalStockUnits = $inventoryItems->sum('quantity');
        $totalStockValuationRetail = $inventoryItems->sum(function ($item) {
            return $item->quantity * ($item->product->wholesale_price ?? 0);
        });
        $totalStockValuationCost = $inventoryItems->sum(function ($item) {
            return $item->quantity * ($item->product->cost_price ?? 0);
        });

        // 3. Low stock alerts
        $products = Product::where('is_active', true)->with('inventories')->get();
        $lowStockProducts = $products->filter(function ($product) use ($selectedStore) {
            $stock = $selectedStore
                ? ($product->inventories->where('store_id', $selectedStore->id)->first()->quantity ?? 0)
                : $product->inventories->sum('quantity');

            return $stock <= $product->reorder_level;
        });

        // 4. Store Network Inventory & Sales Performance
        $storePerformance = $stores->map(function ($store) {
            $units = $store->inventory()->sum('quantity');
            $valuation = $store->inventory()->with('product')->get()->sum(function ($inv) {
                return $inv->quantity * ($inv->product->wholesale_price ?? 0);
            });
            $revenue = SalesInvoice::where('store_id', $store->id)->sum('grand_total');

            return [
                'store' => $store,
                'units' => $units,
                'valuation' => $valuation,
                'revenue' => $revenue,
            ];
        });

        // 5. Recent Stock Audit Ledger Movements (matching screenshot)
        $movementQuery = StockMovement::with(['product', 'store'])->latest()->take(10);
        if ($selectedStore) {
            $movementQuery->where('store_id', $selectedStore->id);
        }
        $recentMovements = $movementQuery->get();

        // 6. Top Selling SKUs
        $topProducts = Product::withCount(['inventories'])
            ->get()
            ->map(function ($p) {
                $unitsSold = SalesInvoice::join('sales_invoice_items', 'sales_invoices.id', '=', 'sales_invoice_items.sales_invoice_id')
                    ->where('sales_invoice_items.product_id', $p->id)
                    ->sum('sales_invoice_items.quantity');
                $revenue = SalesInvoice::join('sales_invoice_items', 'sales_invoices.id', '=', 'sales_invoice_items.sales_invoice_id')
                    ->where('sales_invoice_items.product_id', $p->id)
                    ->sum('sales_invoice_items.total');

                return [
                    'product' => $p,
                    'units_sold' => (int) $unitsSold,
                    'revenue' => (float) $revenue,
                ];
            })
            ->sortByDesc('units_sold')
            ->take(5);

        // 7. Recent Invoices
        $recentInvoices = SalesInvoice::with('customer')->latest()->take(5)->get();

        return view('dashboard.index', compact(
            'stores',
            'selectedStore',
            'totalSalesRevenue',
            'completedOrdersCount',
            'totalStockUnits',
            'totalStockValuationRetail',
            'totalStockValuationCost',
            'lowStockProducts',
            'storePerformance',
            'recentMovements',
            'topProducts',
            'recentInvoices'
        ));
    }
}
