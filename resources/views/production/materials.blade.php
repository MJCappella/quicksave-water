<x-layouts.app>
    <x-slot:title>Material Inventory & Purchases</x-slot:title>

    <div class="space-y-6" x-data="{ openMaterialModal: false, openPurchaseModal: false }">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Material Inventory & Procurement</h1>
                <p class="text-xs text-slate-500 mt-0.5">Preforms, caps, labels, shrink packaging film, and water purification cartridges.</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" @click="openMaterialModal = true"
                        class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 shadow-xs">
                    + Add New Material Item
                </button>
                <button type="button" @click="openPurchaseModal = true"
                        class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Record Material Purchase</span>
                </button>
            </div>
        </div>

        <!-- Raw Materials Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Current Material Stocks</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Real-time balances available for bottling production runs.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4 font-bold">MATERIAL CODE</th>
                            <th class="py-3 px-4 font-bold">ITEM NAME</th>
                            <th class="py-3 px-4 font-bold">CATEGORY</th>
                            <th class="py-3 px-4 font-bold text-right">UNIT COST</th>
                            <th class="py-3 px-4 font-bold text-right">CURRENT STOCK</th>
                            <th class="py-3 px-4 font-bold text-right">REORDER LEVEL</th>
                            <th class="py-3 px-4 font-bold text-right">TOTAL VALUE</th>
                            <th class="py-3 px-4 font-bold text-center">STATUS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($materials as $m)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-slate-800">
                                    {{ $m->code }}
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-slate-900">
                                    {{ $m->name }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-700">
                                        {{ $m->category }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right font-medium text-slate-600">
                                    KES {{ number_format($m->unit_cost, 2) }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-black text-slate-900">
                                    {{ number_format($m->current_stock) }} <span class="text-[10px] font-normal text-slate-500">{{ $m->unit }}</span>
                                </td>
                                <td class="py-3.5 px-4 text-right font-medium text-slate-500">
                                    {{ number_format($m->reorder_level) }} {{ $m->unit }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold text-emerald-700">
                                    KES {{ number_format($m->current_stock * $m->unit_cost, 2) }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if ($m->isLowStock())
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                            Low Stock
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                            Normal
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-6 text-center text-slate-400">No raw materials registered.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Material Purchases History -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Recent Material Purchases</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Supplier shipments of preforms, caps, and packaging items.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4 font-bold">PURCHASE NO</th>
                            <th class="py-3 px-4 font-bold">SUPPLIER</th>
                            <th class="py-3 px-4 font-bold">SUPPLIER INVOICE</th>
                            <th class="py-3 px-4 font-bold">DATE</th>
                            <th class="py-3 px-4 font-bold text-right">TOTAL AMOUNT</th>
                            <th class="py-3 px-4 font-bold text-center">PAYMENT</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($purchases as $p)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-emerald-800">{{ $p->purchase_no }}</td>
                                <td class="py-3.5 px-4 font-semibold text-slate-900">{{ $p->supplier_name }}</td>
                                <td class="py-3.5 px-4 text-slate-600 font-mono text-[11px]">{{ $p->supplier_invoice_no ?? 'N/A' }}</td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $p->purchase_date->format('d M Y') }}</td>
                                <td class="py-3.5 px-4 text-right font-black text-slate-900">KES {{ number_format($p->total_amount, 2) }}</td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        {{ $p->payment_status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-slate-400">No purchases logged.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL: ADD RAW MATERIAL -->
        <div x-cloak x-show="openMaterialModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 border border-slate-100" @click.away="openMaterialModal = false">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-black text-slate-900">Add New Raw Material Item</h3>
                    <button type="button" @click="openMaterialModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                </div>

                <form method="POST" action="{{ route('production.materials.store') }}" class="space-y-3 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Material SKU / Code</label>
                        <input type="text" name="code" required placeholder="e.g. RM-CAP-WHITE"
                               class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Material Name</label>
                        <input type="text" name="name" required placeholder="e.g. 5-Gallon Dispenser White Snap Caps"
                               class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Category</label>
                            <select name="category" class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                                <option value="Preforms">Preforms</option>
                                <option value="Caps & Closures">Caps & Closures</option>
                                <option value="Labels">Labels</option>
                                <option value="Packaging Film">Packaging Film</option>
                                <option value="Treatment Media">Treatment Media</option>
                                <option value="Chemicals">Chemicals</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Unit of Measure</label>
                            <input type="text" name="unit" required placeholder="pcs, rolls, kg, liters" value="pcs"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Unit Cost (KES)</label>
                            <input type="number" step="0.01" name="unit_cost" required placeholder="0.00"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Opening Stock</label>
                            <input type="number" step="0.01" name="current_stock" required placeholder="0"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Reorder Level</label>
                            <input type="number" step="0.01" name="reorder_level" required placeholder="50"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="openMaterialModal = false" class="px-4 py-2 rounded-xl text-slate-600 bg-slate-100 hover:bg-slate-200 font-bold">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black shadow-md shadow-emerald-600/20">
                            Save Material
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: RECORD MATERIAL PURCHASE -->
        <div x-cloak x-show="openPurchaseModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 border border-slate-100" @click.away="openPurchaseModal = false">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-black text-slate-900">Record Material Procurement Purchase</h3>
                    <button type="button" @click="openPurchaseModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                </div>

                <form method="POST" action="{{ route('production.purchases.store') }}" class="space-y-3 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Supplier Name</label>
                        <input type="text" name="supplier_name" required placeholder="e.g. Polypack Industries Ltd"
                               class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Supplier Invoice Ref</label>
                            <input type="text" name="supplier_invoice_no" placeholder="e.g. INV-PLP-9024"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Purchase Date</label>
                            <input type="date" name="purchase_date" required value="{{ date('Y-m-d') }}"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                    </div>

                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 space-y-2">
                        <span class="block font-bold text-slate-800">Purchased Item</span>
                        <div>
                            <label class="block text-[10px] text-slate-500 mb-1">Material</label>
                            <select name="items[0][raw_material_id]" class="w-full rounded-lg border-slate-300 py-1.5 px-2 text-xs">
                                @foreach ($materials as $m)
                                    <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->unit }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[10px] text-slate-500 mb-1">Quantity Received</label>
                                <input type="number" step="0.01" name="items[0][quantity]" required placeholder="10000"
                                       class="w-full rounded-lg border-slate-300 py-1.5 px-2 text-xs">
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-500 mb-1">Unit Cost (KES)</label>
                                <input type="number" step="0.01" name="items[0][unit_cost]" required placeholder="3.80"
                                       class="w-full rounded-lg border-slate-300 py-1.5 px-2 text-xs">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Notes</label>
                        <textarea name="notes" rows="2" placeholder="Quality checks, delivery condition..."
                                  class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="openPurchaseModal = false" class="px-4 py-2 rounded-xl text-slate-600 bg-slate-100 hover:bg-slate-200 font-bold">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black shadow-md shadow-emerald-600/20">
                            Receive & Update Stock
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layouts.app>
