<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentItem;
use App\Models\StockMovement;
use App\Models\Store;
use App\Models\StoreInventory;
use App\Models\StoreTransfer;
use App\Models\StoreTransferItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $selectedStoreId = $request->query('store_id');
        $stores = Store::where('is_active', true)->get();
        $selectedStore = $selectedStoreId ? Store::find($selectedStoreId) : null;

        $inventoryQuery = StoreInventory::with(['store', 'product']);
        if ($selectedStore) {
            $inventoryQuery->where('store_id', $selectedStore->id);
        }
        $inventory = $inventoryQuery->get();

        $products = Product::where('is_active', true)->get();

        $totalUnits = $inventory->sum('quantity');
        $totalValuation = $inventory->sum(fn ($i) => $i->quantity * ($i->product->wholesale_price ?? 0));
        $totalCostValuation = $inventory->sum(fn ($i) => $i->quantity * ($i->product->cost_price ?? 0));

        return view('inventory.index', compact(
            'inventory',
            'stores',
            'selectedStore',
            'products',
            'totalUnits',
            'totalValuation',
            'totalCostValuation'
        ));
    }

    public function transfers(): View
    {
        $transfers = StoreTransfer::with(['fromStore', 'toStore', 'items.product'])->latest()->paginate(15);
        $stores = Store::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();

        return view('inventory.transfers', compact('transfers', 'stores', 'products'));
    }

    public function storeTransfer(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'from_store_id' => 'required|exists:stores,id',
            'to_store_id' => 'required|exists:stores,id|different:from_store_id',
            'transfer_date' => 'required|date',
            'driver_name' => 'nullable|string|max:255',
            'vehicle_reg' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($validated) {
            $transferNo = 'TRF-'.date('Y').'-'.str_pad((string) (StoreTransfer::count() + 1), 4, '0', STR_PAD_LEFT);
            $fromStore = Store::findOrFail($validated['from_store_id']);
            $toStore = Store::findOrFail($validated['to_store_id']);

            $transfer = StoreTransfer::create([
                'transfer_no' => $transferNo,
                'from_store_id' => $fromStore->id,
                'to_store_id' => $toStore->id,
                'transfer_date' => $validated['transfer_date'],
                'status' => 'Completed',
                'driver_name' => $validated['driver_name'] ?? null,
                'vehicle_reg' => $validated['vehicle_reg'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->user()->name ?? 'James Mwangi',
            ]);

            foreach ($validated['items'] as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);
                $qty = (int) $itemData['quantity'];

                StoreTransferItem::create([
                    'store_transfer_id' => $transfer->id,
                    'product_id' => $product->id,
                    'quantity_sent' => $qty,
                    'quantity_received' => $qty,
                ]);

                // Deduct from Source Store
                $fromInv = StoreInventory::firstOrCreate(
                    ['store_id' => $fromStore->id, 'product_id' => $product->id],
                    ['quantity' => 0]
                );
                $fromBalBefore = $fromInv->quantity;
                $fromInv->decrement('quantity', $qty);

                StockMovement::create([
                    'product_id' => $product->id,
                    'store_id' => $fromStore->id,
                    'type' => 'TRANSFER_OUT',
                    'reference_type' => 'Transfer',
                    'reference_no' => $transferNo,
                    'quantity_change' => -$qty,
                    'balance_before' => $fromBalBefore,
                    'balance_after' => $fromInv->fresh()->quantity,
                    'user_name' => auth()->user()->name ?? 'James Mwangi',
                    'notes' => "Transfer to {$toStore->name}",
                ]);

                // Credit to Destination Store
                $toInv = StoreInventory::firstOrCreate(
                    ['store_id' => $toStore->id, 'product_id' => $product->id],
                    ['quantity' => 0]
                );
                $toBalBefore = $toInv->quantity;
                $toInv->increment('quantity', $qty);

                StockMovement::create([
                    'product_id' => $product->id,
                    'store_id' => $toStore->id,
                    'type' => 'TRANSFER_IN',
                    'reference_type' => 'Transfer',
                    'reference_no' => $transferNo,
                    'quantity_change' => $qty,
                    'balance_before' => $toBalBefore,
                    'balance_after' => $toInv->fresh()->quantity,
                    'user_name' => auth()->user()->name ?? 'James Mwangi',
                    'notes' => "Transfer received from {$fromStore->name}",
                ]);
            }
        });

        return redirect()->route('inventory.transfers')->with('success', 'Store transfer executed and dual-store stock ledger updated!');
    }

    public function adjustments(): View
    {
        $adjustments = StockAdjustment::with(['store', 'items.product'])->latest()->paginate(15);
        $stores = Store::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();

        return view('inventory.adjustments', compact('adjustments', 'stores', 'products'));
    }

    public function storeAdjustment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'adjustment_date' => 'required|date',
            'type' => 'required|string|in:ADDITION,DEDUCTION',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($validated) {
            $adjNo = 'ADJ-'.date('Y').'-'.str_pad((string) (StockAdjustment::count() + 1), 4, '0', STR_PAD_LEFT);
            $store = Store::findOrFail($validated['store_id']);
            $isAddition = $validated['type'] === 'ADDITION';
            $totalVal = 0;

            $adjustment = StockAdjustment::create([
                'adjustment_no' => $adjNo,
                'store_id' => $store->id,
                'adjustment_date' => $validated['adjustment_date'],
                'type' => $validated['type'],
                'reason' => $validated['reason'],
                'total_value' => 0,
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->user()->name ?? 'James Mwangi',
            ]);

            foreach ($validated['items'] as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);
                $qty = (int) $itemData['quantity'];
                $unitCost = (float) $product->cost_price;
                $totalVal += $qty * $unitCost;

                StockAdjustmentItem::create([
                    'stock_adjustment_id' => $adjustment->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_cost' => $unitCost,
                ]);

                $inv = StoreInventory::firstOrCreate(
                    ['store_id' => $store->id, 'product_id' => $product->id],
                    ['quantity' => 0]
                );
                $balBefore = $inv->quantity;

                if ($isAddition) {
                    $inv->increment('quantity', $qty);
                    $qtyChange = $qty;
                    $mType = 'ADJUSTMENT_ADD';
                } else {
                    $inv->decrement('quantity', $qty);
                    $qtyChange = -$qty;
                    $mType = 'ADJUSTMENT_SUB';
                }

                StockMovement::create([
                    'product_id' => $product->id,
                    'store_id' => $store->id,
                    'type' => $mType,
                    'reference_type' => 'Adjustment',
                    'reference_no' => $adjNo,
                    'quantity_change' => $qtyChange,
                    'balance_before' => $balBefore,
                    'balance_after' => $inv->fresh()->quantity,
                    'user_name' => auth()->user()->name ?? 'James Mwangi',
                    'notes' => "{$validated['reason']}: {$validated['notes']}",
                ]);
            }

            $adjustment->update(['total_value' => $totalVal]);
        });

        return redirect()->route('inventory.adjustments')->with('success', 'Stock adjustment recorded successfully!');
    }

    public function movements(Request $request): View
    {
        $selectedStoreId = $request->query('store_id');
        $selectedProductId = $request->query('product_id');
        $selectedType = $request->query('type');

        $query = StockMovement::with(['product', 'store'])->latest();

        if ($selectedStoreId) {
            $query->where('store_id', $selectedStoreId);
        }
        if ($selectedProductId) {
            $query->where('product_id', $selectedProductId);
        }
        if ($selectedType) {
            $query->where('type', $selectedType);
        }

        $movements = $query->paginate(25);
        $stores = Store::all();
        $products = Product::all();

        return view('inventory.movements', compact('movements', 'stores', 'products', 'selectedStoreId', 'selectedProductId', 'selectedType'));
    }
}
