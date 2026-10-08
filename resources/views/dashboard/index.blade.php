<x-layouts.app>
    <x-slot:title>Executive Dashboard</x-slot:title>

    <div class="space-y-6">
        
        <!-- Header title block -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Dashboard</h1>
                <p class="text-xs text-slate-500 mt-0.5">Real-time stock valuation, sales metrics, and auditable inventory ledger.</p>
            </div>

            <!-- Store Location filter & Quick POS trigger -->
            <div class="flex items-center gap-2">
                <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <label for="store-select" class="text-xs font-semibold text-slate-600">Store Filter:</label>
                    <select id="store-select" name="store_id" onchange="this.form.submit()"
                            class="text-xs rounded-lg border-slate-300 bg-white py-1.5 px-3 text-slate-700 font-medium shadow-xs focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">All Locations Network</option>
                        @foreach ($stores as $st)
                            <option value="{{ $st->id }}" {{ optional($selectedStore)->id == $st->id ? 'selected' : '' }}>
                                {{ $st->name }} ({{ $st->code }})
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        <!-- 4 TOP KPI METRIC CARDS (Exact match to screenshot) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- 1. Total Sales Revenue -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-[11px] font-bold text-slate-400 tracking-wider uppercase">TOTAL SALES REVENUE</span>
                        <div class="text-2xl font-black text-slate-900 mt-1 tracking-tight">
                            KES {{ number_format($totalSalesRevenue, 2) }}
                        </div>
                        <p class="text-[11px] text-emerald-600 font-medium mt-1 flex items-center gap-1">
                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            From {{ $completedOrdersCount }} completed orders
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center shrink-0 border border-teal-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                </div>
            </div>

            <!-- 2. Total Stock Valuation -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-[11px] font-bold text-slate-400 tracking-wider uppercase">TOTAL STOCK VALUATION</span>
                        <div class="text-2xl font-black text-slate-900 mt-1 tracking-tight">
                            KES {{ number_format($totalStockValuationRetail, 2) }}
                        </div>
                        <p class="text-[11px] text-slate-500 font-medium mt-1">
                            Cost value: <span class="font-semibold text-slate-700">KES {{ number_format($totalStockValuationCost, 2) }}</span>
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                </div>
            </div>

            <!-- 3. Inventory In Stock -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-[11px] font-bold text-slate-400 tracking-wider uppercase">INVENTORY IN STOCK</span>
                        <div class="text-2xl font-black text-slate-900 mt-1 tracking-tight">
                            {{ number_format($totalStockUnits) }} <span class="text-sm font-semibold text-slate-500">units</span>
                        </div>
                        <p class="text-[11px] text-slate-500 font-medium mt-1">
                            Across {{ $stores->count() }} active store location(s)
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0 border border-purple-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                </div>
            </div>

            <!-- 4. Low Stock / Reorders -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-[11px] font-bold text-slate-400 tracking-wider uppercase">LOW STOCK / REORDERS</span>
                        <div class="text-2xl font-black text-slate-900 mt-1 tracking-tight">
                            {{ $lowStockProducts->count() }} <span class="text-sm font-semibold text-slate-500">items</span>
                        </div>
                        <p class="text-[11px] text-amber-600 font-medium mt-1">
                            @if ($lowStockProducts->count() > 0)
                                Action required for replenishing
                            @else
                                All stock levels healthy
                            @endif
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                </div>
            </div>

        </div>

        <!-- MAIN TWO-COLUMN DASHBOARD GRID -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- LEFT 2 COLUMNS: Operational Tables -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Store Network Inventory & Sales Performance (matching screenshot) -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Store Network Inventory & Sales Performance</h2>
                            <p class="text-xs text-slate-400 mt-0.5">Live operational breakdown by store location.</p>
                        </div>
                        <a href="{{ route('inventory.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">
                            View All Inventory &rarr;
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50/75 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-100">
                                <tr>
                                    <th class="py-3 px-4 font-bold">STORE LOCATION</th>
                                    <th class="py-3 px-4 font-bold">BRANCH</th>
                                    <th class="py-3 px-4 font-bold text-right">STOCK UNITS</th>
                                    <th class="py-3 px-4 font-bold text-right">STOCK VALUATION</th>
                                    <th class="py-3 px-4 font-bold text-right">TOTAL REVENUE</th>
                                    <th class="py-3 px-4 font-bold text-center">ACTION</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($storePerformance as $perf)
                                    <tr class="hover:bg-slate-50/50 transition">
                                        <td class="py-3.5 px-4 font-semibold text-slate-900">
                                            {{ $perf['store']->name }}
                                            <div class="text-[10px] text-slate-400 font-normal">{{ $perf['store']->code }}</div>
                                        </td>
                                        <td class="py-3.5 px-4 text-slate-600">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-700">
                                                {{ $perf['store']->type }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-right font-bold text-slate-900">
                                            {{ number_format($perf['units']) }}
                                        </td>
                                        <td class="py-3.5 px-4 text-right font-medium text-slate-800">
                                            KES {{ number_format($perf['valuation'], 2) }}
                                        </td>
                                        <td class="py-3.5 px-4 text-right font-semibold text-emerald-700">
                                            KES {{ number_format($perf['revenue'], 2) }}
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            <a href="{{ route('inventory.index', ['store_id' => $perf['store']->id]) }}"
                                               class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                                                View Store
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-6 text-center text-slate-400">No store locations registered yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Stock Audit Ledger Movements (Exact match to screenshot) -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Recent Stock Audit Ledger Movements</h2>
                            <p class="text-xs text-slate-400 mt-0.5">Real-time double-entry inventory transactions.</p>
                        </div>
                        <a href="{{ route('inventory.movements') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">
                            View Full Ledger &rarr;
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50/75 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-100">
                                <tr>
                                    <th class="py-3 px-4 font-bold">TIMESTAMP</th>
                                    <th class="py-3 px-4 font-bold">PRODUCT / SKU</th>
                                    <th class="py-3 px-4 font-bold">STORE</th>
                                    <th class="py-3 px-4 font-bold text-center">TYPE</th>
                                    <th class="py-3 px-4 font-bold text-right">QUANTITY CHANGE</th>
                                    <th class="py-3 px-4 font-bold text-right">BALANCE AFTER</th>
                                    <th class="py-3 px-4 font-bold">USER</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($recentMovements as $mov)
                                    <tr class="hover:bg-slate-50/50 transition">
                                        <td class="py-3.5 px-4 text-slate-500 whitespace-nowrap">
                                            <div class="font-medium text-slate-700">{{ $mov->created_at->format('M d,') }}</div>
                                            <div class="text-[10px] text-slate-400">{{ $mov->created_at->format('H:i') }}</div>
                                        </td>
                                        <td class="py-3.5 px-4 font-semibold text-slate-900">
                                            <div class="truncate max-w-[200px]" title="{{ $mov->product->name ?? 'N/A' }}">
                                                {{ $mov->product->name ?? 'N/A' }}
                                            </div>
                                            <div class="text-[10px] text-slate-400 font-mono">{{ $mov->product->sku ?? '' }}</div>
                                        </td>
                                        <td class="py-3.5 px-4 text-slate-600 whitespace-nowrap">
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
                                        <td class="py-3.5 px-4 text-right font-black {{ $mov->quantity_change > 0 ? 'text-emerald-600' : 'text-slate-800' }}">
                                            {{ $mov->quantity_change > 0 ? '+' : '' }}{{ $mov->quantity_change }}
                                        </td>
                                        <td class="py-3.5 px-4 text-right font-bold text-slate-900">
                                            {{ $mov->balance_after }}
                                        </td>
                                        <td class="py-3.5 px-4 text-slate-500 truncate max-w-[120px]">
                                            {{ $mov->user_name }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-6 text-center text-slate-400">No stock movements logged yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- RIGHT 1 COLUMN: Alerts & Top Sellers (matching screenshot) -->
            <div class="space-y-6">
                
                <!-- Low Stock Alert Box -->
                <div class="bg-amber-50/60 rounded-2xl border border-amber-200/80 p-5 shadow-xs">
                    <div class="flex items-center justify-between pb-3 border-b border-amber-200/60">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            <span class="text-xs font-bold text-amber-900 uppercase tracking-wide">Low Stock Alerts</span>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-200/80 text-amber-900">
                            {{ $lowStockProducts->count() }} NEEDS ATTENTION
                        </span>
                    </div>

                    <div class="pt-3">
                        @if ($lowStockProducts->count() > 0)
                            <div class="space-y-2.5">
                                @foreach ($lowStockProducts->take(4) as $lowP)
                                    <div class="flex items-center justify-between text-xs bg-white/70 p-2.5 rounded-xl border border-amber-200/50">
                                        <div class="min-w-0 pr-2">
                                            <p class="font-bold text-slate-800 truncate">{{ $lowP->name }}</p>
                                            <p class="text-[10px] text-slate-400">Reorder limit: {{ $lowP->reorder_level }}</p>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <span class="font-black text-rose-600">{{ $lowP->total_stock }}</span>
                                            <span class="text-[10px] text-slate-400 block">in stock</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4 text-xs text-slate-500 font-medium">
                                All stock levels are above reorder thresholds.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Top Selling SKUs (matching screenshot) -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Top Selling SKUs</h3>
                        <span class="text-[10px] text-slate-400">By units sold</span>
                    </div>

                    <div class="pt-3 divide-y divide-slate-100">
                        @forelse ($topProducts as $idx => $item)
                            <div class="py-3 flex items-start gap-3">
                                <div class="w-6 h-6 rounded-full bg-slate-100 text-slate-600 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">
                                    {{ $idx + 1 }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-slate-900 truncate">{{ $item['product']->name }}</p>
                                    <p class="text-[10px] text-slate-400">{{ $item['product']->category }}</p>
                                </div>
                                <div class="text-right shrink-0">
                                    <div class="text-xs font-bold text-slate-800">{{ $item['units_sold'] }} sold</div>
                                    <div class="text-[11px] font-semibold text-emerald-600">KES {{ number_format($item['revenue'], 2) }}</div>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 py-3 text-center">No sales recorded yet.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Recent POS Invoices Box -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Recent POS Invoices</h3>
                        <a href="{{ route('sales.invoices') }}" class="text-[11px] font-semibold text-emerald-600 hover:text-emerald-700">All Sales &rarr;</a>
                    </div>

                    <div class="pt-2 divide-y divide-slate-100">
                        @forelse ($recentInvoices as $inv)
                            <div class="py-2.5 flex items-center justify-between text-xs">
                                <div>
                                    <div class="font-mono font-bold text-slate-800">{{ $inv->invoice_no }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $inv->customer->name ?? 'Walk-In' }} • {{ $inv->invoice_date->format('M d') }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-slate-900">KES {{ number_format($inv->grand_total, 2) }}</div>
                                    <a href="{{ route('pos.receipt', $inv->id) }}" class="text-[10px] text-emerald-600 hover:underline">
                                        Receipt
                                    </a>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 py-3 text-center">No recent POS sales.</p>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>

    </div>
</x-layouts.app>
