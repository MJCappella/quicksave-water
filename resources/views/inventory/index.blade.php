<x-layouts.app>
    <x-slot:title>Finished Products Inventory</x-slot:title>

    <div class="space-y-6">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Finished Products Inventory</h1>
                <p class="text-xs text-slate-500 mt-0.5">Real-time balances across bottling plants, distribution depots, and mobile van trucks.</p>
            </div>
            
            <div class="flex items-center gap-2">
                <a href="{{ route('inventory.transfers') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 shadow-xs flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    <span>Store Transfers</span>
                </a>
                <a href="{{ route('inventory.adjustments') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 shadow-xs flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                    <span>Stock Adjustments</span>
                </a>
            </div>
        </div>

        <!-- Filter bar & Location summary -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <form method="GET" action="{{ route('inventory.index') }}" class="flex items-center gap-2">
                <label for="filter-store" class="text-xs font-bold text-slate-600">Location Filter:</label>
                <select id="filter-store" name="store_id" onchange="this.form.submit()"
                        class="text-xs rounded-xl border-slate-300 bg-white py-1.5 px-3 text-slate-800 font-semibold focus:border-emerald-500">
                    <option value="">All Warehouses & Van Stores</option>
                    @foreach ($stores as $st)
                        <option value="{{ $st->id }}" {{ optional($selectedStore)->id == $st->id ? 'selected' : '' }}>
                            {{ $st->name }} ({{ $st->type }})
                        </option>
                    @endforeach
                </select>
            </form>

            <div class="flex items-center gap-4 text-xs">
                <div>
                    <span class="text-slate-400">Total Units:</span>
                    <span class="font-black text-slate-900 ml-1">{{ number_format($totalUnits) }}</span>
                </div>
                <div>
                    <span class="text-slate-400">Retail Value:</span>
                    <span class="font-black text-emerald-700 ml-1">KES {{ number_format($totalValuation, 2) }}</span>
                </div>
                <div>
                    <span class="text-slate-400">Cost Value:</span>
                    <span class="font-black text-slate-700 ml-1">KES {{ number_format($totalCostValuation, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Inventory Balances Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4 font-bold">STORE / LOCATION</th>
                            <th class="py-3 px-4 font-bold">SKU</th>
                            <th class="py-3 px-4 font-bold">PRODUCT DESCRIPTION</th>
                            <th class="py-3 px-4 font-bold">CATEGORY</th>
                            <th class="py-3 px-4 font-bold text-right">COST PRICE</th>
                            <th class="py-3 px-4 font-bold text-right">WHOLESALE</th>
                            <th class="py-3 px-4 font-bold text-right">QUANTITY IN STOCK</th>
                            <th class="py-3 px-4 font-bold text-right">TOTAL VALUATION</th>
                            <th class="py-3 px-4 font-bold text-center">STOCK HEALTH</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($inventory as $item)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3.5 px-4 font-semibold text-slate-800">
                                    {{ $item->store->name ?? 'N/A' }}
                                    <div class="text-[10px] text-slate-400 font-normal">{{ $item->store->code ?? '' }}</div>
                                </td>
                                <td class="py-3.5 px-4 font-mono font-bold text-slate-700">
                                    {{ $item->product->sku ?? '' }}
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-slate-900">
                                    {{ $item->product->name ?? '' }}
                                    <div class="text-[10px] text-slate-400 font-normal">{{ $item->product->unit_measure ?? '' }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-700">
                                        {{ $item->product->category ?? '' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right text-slate-600">
                                    KES {{ number_format($item->product->cost_price ?? 0, 2) }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-medium text-slate-800">
                                    KES {{ number_format($item->product->wholesale_price ?? 0, 2) }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-black text-slate-900 text-sm">
                                    {{ number_format($item->quantity) }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold text-emerald-700">
                                    KES {{ number_format($item->quantity * ($item->product->wholesale_price ?? 0), 2) }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if ($item->quantity <= ($item->product->reorder_level ?? 10))
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                            Low Stock
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                            Optimal
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-8 text-center text-slate-400">No stock entries found for this location.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-layouts.app>
