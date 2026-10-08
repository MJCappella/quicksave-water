<x-layouts.app>
    <x-slot:title>Production & Bottling Batches</x-slot:title>

    <div class="space-y-6" x-data="{ openBatchModal: false }">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Production & Packaging Batches</h1>
                <p class="text-xs text-slate-500 mt-0.5">Automated RO water bottling runs, yield accounting, and material utilization.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('production.materials') }}" 
                   class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 shadow-xs">
                    Material Inventory
                </a>
                <button type="button" @click="openBatchModal = true"
                        class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Log New Production Run</span>
                </button>
            </div>
        </div>

        <!-- Metrics Overview -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">MONTHLY PRODUCTION YIELD</span>
                <div class="text-2xl font-black text-slate-900 mt-1">
                    {{ number_format($totalProducedMonth) }} <span class="text-sm font-semibold text-slate-500">units</span>
                </div>
                <p class="text-[11px] text-emerald-600 font-medium mt-1">Pure drinking water finished goods</p>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">RAW MATERIALS VALUATION</span>
                <div class="text-2xl font-black text-slate-900 mt-1">
                    KES {{ number_format($rawMaterialValuation, 2) }}
                </div>
                <p class="text-[11px] text-slate-500 font-medium mt-1">Preforms, caps, labels & chemicals</p>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">MATERIALS BELOW REORDER</span>
                <div class="text-2xl font-black text-slate-900 mt-1">
                    {{ $lowStockMaterialsCount }} <span class="text-sm font-semibold text-slate-500">items</span>
                </div>
                <p class="text-[11px] text-amber-600 font-medium mt-1">Need procurement attention</p>
            </div>
        </div>

        <!-- Production Batches Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Recorded Production Batches</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Real-time yield updates to warehouse stock.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4 font-bold">BATCH NO</th>
                            <th class="py-3 px-4 font-bold">DATE & SHIFT</th>
                            <th class="py-3 px-4 font-bold">TARGET PRODUCT</th>
                            <th class="py-3 px-4 font-bold">DESTINATION STORE</th>
                            <th class="py-3 px-4 font-bold text-right">PLANNED</th>
                            <th class="py-3 px-4 font-bold text-right">YIELD</th>
                            <th class="py-3 px-4 font-bold text-right">REJECTED</th>
                            <th class="py-3 px-4 font-bold text-right">EFFICIENCY</th>
                            <th class="py-3 px-4 font-bold">SUPERVISOR</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($batches as $b)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-emerald-800">
                                    {{ $b->batch_no }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    <div class="font-medium text-slate-800">{{ $b->batch_date->format('d M Y') }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $b->shift }}</div>
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-slate-900">
                                    {{ $b->product->name ?? 'N/A' }}
                                    <div class="text-[10px] text-slate-400 font-mono">{{ $b->product->sku ?? '' }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    {{ $b->store->name ?? 'Main Plant' }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-medium text-slate-600">
                                    {{ number_format($b->planned_quantity) }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-black text-emerald-600">
                                    {{ number_format($b->actual_yield) }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold text-rose-500">
                                    {{ number_format($b->rejected_quantity) }}
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ $b->efficiency_rate >= 95 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $b->efficiency_rate }}%
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    {{ $b->supervisor_name }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-8 text-center text-slate-400">No production batches recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($batches->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $batches->links() }}
                </div>
            @endif
        </div>

        <!-- MODAL: LOG NEW PRODUCTION BATCH -->
        <div x-cloak x-show="openBatchModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl space-y-4 border border-slate-100" @click.away="openBatchModal = false">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="text-base font-black text-slate-900">Log Water Production & Packaging Batch</h3>
                        <p class="text-xs text-slate-400">Yield is automatically credited to the selected store stock.</p>
                    </div>
                    <button type="button" @click="openBatchModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                </div>

                <form method="POST" action="{{ route('production.batches.store') }}" class="space-y-4 text-xs">
                    @csrf
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Target Product</label>
                            <select name="product_id" required class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                                @foreach ($products as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->sku }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Receiving Store / Warehouse</label>
                            <select name="store_id" required class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                                @foreach ($stores as $st)
                                    <option value="{{ $st->id }}">{{ $st->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Planned Quantity</label>
                            <input type="number" name="planned_quantity" required min="1" placeholder="e.g. 200"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Actual Yield (Finished Units)</label>
                            <input type="number" name="actual_yield" required min="0" placeholder="e.g. 198"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Defective / Rejected Units</label>
                            <input type="number" name="rejected_quantity" value="0" min="0"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Batch Date</label>
                            <input type="date" name="batch_date" required value="{{ date('Y-m-d') }}"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Operational Shift</label>
                            <select name="shift" class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                                <option value="Day Shift (07:00 - 16:00)">Day Shift (07:00 - 16:00)</option>
                                <option value="Night Shift (16:00 - 00:00)">Night Shift (16:00 - 00:00)</option>
                                <option value="Overtime Weekend Shift">Overtime Weekend Shift</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Plant Supervisor</label>
                            <input type="text" name="supervisor_name" value="Dennis Kipchumba" required
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Water Treatment / RO Meter Reading</label>
                        <input type="text" name="water_source_reading" placeholder="e.g. RO Initial: 45,000L; Final: 49,800L (TDS: 28 ppm)"
                               class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                    </div>

                    <!-- Material consumption deduction -->
                    <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200">
                        <span class="block font-bold text-slate-800 mb-2">Primary Raw Material Usage Deduction</span>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="block text-[10px] text-slate-500 mb-1">Component</label>
                                <select name="usages[0][raw_material_id]" class="w-full rounded-lg border-slate-300 py-1.5 px-2 text-xs">
                                    @foreach ($rawMaterials as $rm)
                                        <option value="{{ $rm->id }}">{{ $rm->name }} ({{ $rm->current_stock }} {{ $rm->unit }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-500 mb-1">Qty Used</label>
                                <input type="number" step="0.01" name="usages[0][quantity_used]" value="200"
                                       class="w-full rounded-lg border-slate-300 py-1.5 px-2 text-xs">
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-500 mb-1">Qty Wasted</label>
                                <input type="number" step="0.01" name="usages[0][quantity_wasted]" value="2"
                                       class="w-full rounded-lg border-slate-300 py-1.5 px-2 text-xs">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="openBatchModal = false" class="px-4 py-2 rounded-xl text-slate-600 bg-slate-100 hover:bg-slate-200 font-bold">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black shadow-md shadow-emerald-600/20">
                            Save Batch & Update Stock
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layouts.app>
