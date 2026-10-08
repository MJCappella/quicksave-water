<x-layouts.app>
    <x-slot:title>Stock Movement Audit Ledger</x-slot:title>

    <div class="space-y-6">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Stock Movement Audit Ledger</h1>
                <p class="text-xs text-slate-500 mt-0.5">Chronological double-entry audit trail tracking every bottle in and out of the company.</p>
            </div>
            
            <a href="{{ route('inventory.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 shadow-xs">
                &larr; Back to Inventory
            </a>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <form method="GET" action="{{ route('inventory.movements') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
                <div>
                    <label class="block font-bold text-slate-600 mb-1">Filter Store</label>
                    <select name="store_id" onchange="this.form.submit()" class="w-full rounded-xl border-slate-300 py-1.5 px-3">
                        <option value="">All Locations</option>
                        @foreach ($stores as $st)
                            <option value="{{ $st->id }}" {{ $selectedStoreId == $st->id ? 'selected' : '' }}>
                                {{ $st->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-600 mb-1">Filter Product</label>
                    <select name="product_id" onchange="this.form.submit()" class="w-full rounded-xl border-slate-300 py-1.5 px-3">
                        <option value="">All Products</option>
                        @foreach ($products as $p)
                            <option value="{{ $p->id }}" {{ $selectedProductId == $p->id ? 'selected' : '' }}>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-600 mb-1">Movement Type</label>
                    <select name="type" onchange="this.form.submit()" class="w-full rounded-xl border-slate-300 py-1.5 px-3">
                        <option value="">All Types</option>
                        <option value="SALE" {{ $selectedType == 'SALE' ? 'selected' : '' }}>SALE</option>
                        <option value="PRODUCTION" {{ $selectedType == 'PRODUCTION' ? 'selected' : '' }}>PRODUCTION</option>
                        <option value="TRANSFER_IN" {{ $selectedType == 'TRANSFER_IN' ? 'selected' : '' }}>TRANSFER IN</option>
                        <option value="TRANSFER_OUT" {{ $selectedType == 'TRANSFER_OUT' ? 'selected' : '' }}>TRANSFER OUT</option>
                        <option value="ADJUSTMENT_ADD" {{ $selectedType == 'ADJUSTMENT_ADD' ? 'selected' : '' }}>ADJUSTMENT ADD</option>
                        <option value="ADJUSTMENT_SUB" {{ $selectedType == 'ADJUSTMENT_SUB' ? 'selected' : '' }}>ADJUSTMENT SUB</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <a href="{{ route('inventory.movements') }}" class="w-full py-2 text-center rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                        Reset Filters
                    </a>
                </div>
            </form>
        </div>

        <!-- Ledger Table (Matching screenshot layout) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4 font-bold">TIMESTAMP</th>
                            <th class="py-3 px-4 font-bold">PRODUCT / SKU</th>
                            <th class="py-3 px-4 font-bold">STORE</th>
                            <th class="py-3 px-4 font-bold text-center">TYPE</th>
                            <th class="py-3 px-4 font-bold">REFERENCE</th>
                            <th class="py-3 px-4 font-bold text-right">QUANTITY CHANGE</th>
                            <th class="py-3 px-4 font-bold text-right">BEFORE</th>
                            <th class="py-3 px-4 font-bold text-right">AFTER</th>
                            <th class="py-3 px-4 font-bold">USER</th>
                            <th class="py-3 px-4 font-bold">NOTES</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($movements as $mov)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3.5 px-4 text-slate-500 whitespace-nowrap">
                                    <div class="font-medium text-slate-800">{{ $mov->created_at->format('M d, Y') }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">{{ $mov->created_at->format('H:i:s') }}</div>
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-slate-900">
                                    <div>{{ $mov->product->name ?? 'N/A' }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">{{ $mov->product->sku ?? '' }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-slate-700 font-medium whitespace-nowrap">
                                    {{ $mov->store->name ?? 'N/A' }}
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    @php
                                        $badgeColors = [
                                            'SALE' => 'bg-sky-100 text-sky-800 border-sky-200',
                                            'PRODUCTION' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                            'TRANSFER_IN' => 'bg-teal-100 text-teal-800 border-teal-200',
                                            'TRANSFER_OUT' => 'bg-amber-100 text-amber-800 border-amber-200',
                                            'ADJUSTMENT_ADD' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                            'ADJUSTMENT_SUB' => 'bg-rose-100 text-rose-800 border-rose-200',
                                        ];
                                        $cls = $badgeColors[$mov->type] ?? 'bg-slate-100 text-slate-800 border-slate-200';
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold border {{ $cls }}">
                                        {{ $mov->type }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-slate-700 whitespace-nowrap">
                                    <span class="text-[10px] text-slate-400 block">{{ $mov->reference_type }}</span>
                                    <span class="font-bold">{{ $mov->reference_no ?? 'N/A' }}</span>
                                </td>
                                <td class="py-3.5 px-4 text-right font-black {{ $mov->quantity_change > 0 ? 'text-emerald-600' : 'text-slate-800' }}">
                                    {{ $mov->quantity_change > 0 ? '+' : '' }}{{ $mov->quantity_change }}
                                </td>
                                <td class="py-3.5 px-4 text-right text-slate-500 font-mono">
                                    {{ $mov->balance_before }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold text-slate-900 font-mono">
                                    {{ $mov->balance_after }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 truncate max-w-[120px]">
                                    {{ $mov->user_name }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-500 text-[11px] truncate max-w-[180px]">
                                    {{ $mov->notes }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-8 text-center text-slate-400">No stock movements match current filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($movements->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $movements->links() }}
                </div>
            @endif
        </div>

    </div>
</x-layouts.app>
