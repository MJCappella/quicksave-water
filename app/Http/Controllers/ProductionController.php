<?php

namespace App\Http\Controllers;

use App\Models\MaterialPurchase;
use App\Models\MaterialPurchaseItem;
use App\Models\Product;
use App\Models\ProductionBatch;
use App\Models\ProductionMaterialUsage;
use App\Models\RawMaterial;
use App\Models\StockMovement;
use App\Models\Store;
use App\Models\StoreInventory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductionController extends Controller
{
    public function index(): View
    {
        $batches = ProductionBatch::with(['product', 'store', 'usages.rawMaterial'])->latest()->paginate(15);
        $rawMaterials = RawMaterial::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();
        $stores = Store::where('is_active', true)->get();

        $totalProducedMonth = ProductionBatch::whereMonth('batch_date', now()->month)->sum('actual_yield');
        $rawMaterialValuation = $rawMaterials->sum(fn ($m) => $m->current_stock * $m->unit_cost);
        $lowStockMaterialsCount = $rawMaterials->filter(fn ($m) => $m->isLowStock())->count();

        return view('production.index', compact(
            'batches',
            'rawMaterials',
            'products',
            'stores',
            'totalProducedMonth',
            'rawMaterialValuation',
            'lowStockMaterialsCount'
        ));
    }

    public function materials(): View
    {
        $materials = RawMaterial::withCount(['usages', 'purchaseItems'])->latest()->get();
        $purchases = MaterialPurchase::with('items.rawMaterial')->latest()->take(10)->get();

        return view('production.materials', compact('materials', 'purchases'));
    }

    public function storeMaterial(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:raw_materials,code',
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'unit' => 'required|string|max:50',
            'unit_cost' => 'required|numeric|min:0',
            'current_stock' => 'required|numeric|min:0',
            'reorder_level' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        RawMaterial::create($validated);

        return redirect()->route('production.materials')->with('success', 'Raw material added successfully!');
    }

    public function storePurchase(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'supplier_invoice_no' => 'nullable|string|max:100',
            'purchase_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.raw_material_id' => 'required|exists:raw_materials,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $purchaseNo = 'MP-'.date('Y').'-'.str_pad((string) (MaterialPurchase::count() + 1), 4, '0', STR_PAD_LEFT);
            $totalAmount = 0;

            foreach ($validated['items'] as $item) {
                $totalAmount += $item['quantity'] * $item['unit_cost'];
            }

            $purchase = MaterialPurchase::create([
                'purchase_no' => $purchaseNo,
                'supplier_name' => $validated['supplier_name'],
                'supplier_invoice_no' => $validated['supplier_invoice_no'] ?? null,
                'purchase_date' => $validated['purchase_date'],
                'status' => 'Received',
                'total_amount' => $totalAmount,
                'payment_status' => 'Paid',
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $material = RawMaterial::findOrFail($item['raw_material_id']);
                $qty = (float) $item['quantity'];
                $cost = (float) $item['unit_cost'];
                $lineTotal = $qty * $cost;

                MaterialPurchaseItem::create([
                    'material_purchase_id' => $purchase->id,
                    'raw_material_id' => $material->id,
                    'quantity' => $qty,
                    'unit_cost' => $cost,
                    'total_cost' => $lineTotal,
                ]);

                // Update raw material inventory
                $material->increment('current_stock', $qty);
                $material->update(['unit_cost' => $cost]);
            }
        });

        return redirect()->route('production.materials')->with('success', 'Material purchase recorded and inventory updated!');
    }

    public function storeBatch(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'store_id' => 'required|exists:stores,id',
            'planned_quantity' => 'required|integer|min:1',
            'actual_yield' => 'required|integer|min:0',
            'rejected_quantity' => 'nullable|integer|min:0',
            'batch_date' => 'required|date',
            'shift' => 'required|string',
            'supervisor_name' => 'required|string|max:255',
            'water_source_reading' => 'nullable|string',
            'notes' => 'nullable|string',
            'usages' => 'nullable|array',
            'usages.*.raw_material_id' => 'required_with:usages|exists:raw_materials,id',
            'usages.*.quantity_used' => 'required_with:usages|numeric|min:0',
            'usages.*.quantity_wasted' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            $batchNo = 'BAT-'.date('Y').'-'.str_pad((string) (ProductionBatch::count() + 101), 4, '0', STR_PAD_LEFT);
            $product = Product::findOrFail($validated['product_id']);
            $store = Store::findOrFail($validated['store_id']);
            $actualYield = (int) $validated['actual_yield'];
            $rejected = (int) ($validated['rejected_quantity'] ?? 0);

            $totalProductionCost = 0;

            $batch = ProductionBatch::create([
                'batch_no' => $batchNo,
                'product_id' => $product->id,
                'store_id' => $store->id,
                'planned_quantity' => $validated['planned_quantity'],
                'actual_yield' => $actualYield,
                'rejected_quantity' => $rejected,
                'batch_date' => $validated['batch_date'],
                'status' => 'Completed',
                'production_cost' => 0,
                'supervisor_name' => $validated['supervisor_name'],
                'shift' => $validated['shift'],
                'water_source_reading' => $validated['water_source_reading'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Deduct raw material usages
            if (! empty($validated['usages'])) {
                foreach ($validated['usages'] as $usageData) {
                    $material = RawMaterial::findOrFail($usageData['raw_material_id']);
                    $used = (float) $usageData['quantity_used'];
                    $wasted = (float) ($usageData['quantity_wasted'] ?? 0);
                    $totalMaterialDeduction = $used + $wasted;
                    $cost = $used * (float) $material->unit_cost;
                    $totalProductionCost += $cost;

                    ProductionMaterialUsage::create([
                        'production_batch_id' => $batch->id,
                        'raw_material_id' => $material->id,
                        'quantity_used' => $used,
                        'quantity_wasted' => $wasted,
                        'unit_cost' => $material->unit_cost,
                        'total_cost' => $cost,
                    ]);

                    $material->decrement('current_stock', $totalMaterialDeduction);
                }
            }

            $batch->update(['production_cost' => $totalProductionCost]);

            // Add finished product yield to Store Inventory
            $inv = StoreInventory::firstOrCreate(
                ['store_id' => $store->id, 'product_id' => $product->id],
                ['quantity' => 0]
            );

            $balBefore = $inv->quantity;
            $inv->increment('quantity', $actualYield);
            $balAfter = $inv->fresh()->quantity;

            // Audit ledger movement
            StockMovement::create([
                'product_id' => $product->id,
                'store_id' => $store->id,
                'type' => 'PRODUCTION',
                'reference_type' => 'Batch',
                'reference_no' => $batchNo,
                'quantity_change' => $actualYield,
                'balance_before' => $balBefore,
                'balance_after' => $balAfter,
                'user_name' => $validated['supervisor_name'],
                'notes' => "Production yield from batch {$batchNo}",
            ]);
        });

        return redirect()->route('production.index')->with('success', 'Production batch logged and finished inventory credited!');
    }

    public function utilization(): View
    {
        $usages = ProductionMaterialUsage::with(['batch.product', 'rawMaterial'])->latest()->paginate(20);
        $materialSummaries = RawMaterial::all()->map(function ($material) {
            $totalUsed = ProductionMaterialUsage::where('raw_material_id', $material->id)->sum('quantity_used');
            $totalWasted = ProductionMaterialUsage::where('raw_material_id', $material->id)->sum('quantity_wasted');
            $wasteRate = ($totalUsed + $totalWasted) > 0 ? round(($totalWasted / ($totalUsed + $totalWasted)) * 100, 2) : 0;

            return [
                'material' => $material,
                'total_used' => $totalUsed,
                'total_wasted' => $totalWasted,
                'waste_rate' => $wasteRate,
            ];
        });

        return view('production.utilization', compact('usages', 'materialSummaries'));
    }
}
