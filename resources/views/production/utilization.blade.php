<x-layouts.app>
    <x-slot:title>Material Utilization Tracking</x-slot:title>

    <div class="space-y-6">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Material Utilization & Wastage Tracking</h1>
                <p class="text-xs text-slate-500 mt-0.5">Component consumption analysis per bottling run, scrap rates, and efficiency metrics.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('production.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 shadow-xs">
                    &larr; Back to Batches
                </a>
            </div>
        </div>

        <!-- Material Consumption & Scrap Rates Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ($materialSummaries->take(4) as $sum)
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider truncate block">
                        {{ $sum['material']->name }}
                    </span>
                    <div class="flex items-baseline justify-between mt-1">
                        <div class="text-xl font-black text-slate-900">
                            {{ number_format($sum['total_used']) }} <span class="text-xs font-normal text-slate-500">{{ $sum['material']->unit }}</span>
                        </div>
                        <span class="text-xs font-bold {{ $sum['waste_rate'] > 2 ? 'text-rose-600' : 'text-emerald-600' }}">
                            {{ $sum['waste_rate'] }}% scrap
                        </span>
                    </div>
                    <div class="mt-2 text-[11px] text-slate-500 flex justify-between">
                        <span>Wasted: {{ number_format($sum['total_wasted']) }} {{ $sum['material']->unit }}</span>
                        <span class="font-semibold text-slate-700">Cost: KES {{ number_format($sum['total_wasted'] * $sum['material']->unit_cost, 2) }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Detailed Utilization Log Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Batch-by-Batch Material Consumption Log</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Every unit used, defective components, and cost per batch run.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4 font-bold">BATCH REF</th>
                            <th class="py-3 px-4 font-bold">FINISHED PRODUCT</th>
                            <th class="py-3 px-4 font-bold">COMPONENT / MATERIAL</th>
                            <th class="py-3 px-4 font-bold text-right">QUANTITY USED</th>
                            <th class="py-3 px-4 font-bold text-right">QUANTITY WASTED</th>
                            <th class="py-3 px-4 font-bold text-right">UNIT COST</th>
                            <th class="py-3 px-4 font-bold text-right">TOTAL MATERIAL COST</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($usages as $u)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-emerald-800">
                                    {{ $u->batch->batch_no ?? 'N/A' }}
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-slate-900">
                                    {{ $u->batch->product->name ?? 'N/A' }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-800">
                                    <div class="font-medium">{{ $u->rawMaterial->name ?? 'N/A' }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">{{ $u->rawMaterial->code ?? '' }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-right font-black text-slate-900">
                                    {{ number_format($u->quantity_used) }} {{ $u->rawMaterial->unit ?? '' }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold text-rose-600">
                                    {{ number_format($u->quantity_wasted) }} {{ $u->rawMaterial->unit ?? '' }}
                                </td>
                                <td class="py-3.5 px-4 text-right text-slate-600">
                                    KES {{ number_format($u->unit_cost, 2) }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-black text-slate-900">
                                    KES {{ number_format($u->total_cost, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-6 text-center text-slate-400">No utilization logged yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($usages->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $usages->links() }}
                </div>
            @endif
        </div>

    </div>
</x-layouts.app>
