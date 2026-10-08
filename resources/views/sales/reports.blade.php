<x-layouts.app>
    <x-slot:title>Sales & Revenue Reports</x-slot:title>

    <div class="space-y-6">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Sales & Revenue Performance Reports</h1>
                <p class="text-xs text-slate-500 mt-0.5">Comprehensive audit reports covering sales volume, cashier counter settlements, and commercial billing.</p>
            </div>
            
            <button type="button" onclick="window.print()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 shadow-xs flex items-center gap-1.5">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Print Sales Audit Report</span>
            </button>
        </div>

        <!-- Date Range Filter -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <form method="GET" action="{{ route('sales.reports') }}" class="flex flex-wrap items-end gap-3 text-xs">
                <div>
                    <label class="block font-bold text-slate-600 mb-1">Start Date</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="rounded-xl border-slate-300 py-1.5 px-3 text-xs focus:border-emerald-500">
                </div>
                <div>
                    <label class="block font-bold text-slate-600 mb-1">End Date</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="rounded-xl border-slate-300 py-1.5 px-3 text-xs focus:border-emerald-500">
                </div>
                <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold transition shadow-xs">
                    Apply Filter
                </button>
                <a href="{{ route('sales.reports') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                    Reset
                </a>
            </form>
        </div>

        <!-- Summary KPIs -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">TOTAL INVOICED REVENUE</span>
                <div class="text-2xl font-black text-slate-900 mt-1">
                    KES {{ number_format($totalSales, 2) }}
                </div>
                <p class="text-[11px] text-slate-500 mt-0.5">{{ $invoices->count() }} sales transactions recorded</p>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">TOTAL COLLECTED (CASH/M-PESA)</span>
                <div class="text-2xl font-black text-emerald-700 mt-1">
                    KES {{ number_format($totalPaid, 2) }}
                </div>
                <p class="text-[11px] text-emerald-600 mt-0.5">Liquid cash inflows settled</p>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-[11px] font-bold text-rose-600 uppercase tracking-wider">OUTSTANDING PENDING RECEIVABLES</span>
                <div class="text-2xl font-black text-rose-600 mt-1">
                    KES {{ number_format($totalPending, 2) }}
                </div>
                <p class="text-[11px] text-slate-500 mt-0.5">Commercial credit due from clients</p>
            </div>
        </div>

        <!-- Sales Invoices Detailed List -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-900">Sales Transactions Register</h2>
                <p class="text-xs text-slate-400 mt-0.5">From {{ date('d M Y', strtotime($startDate)) }} to {{ date('d M Y', strtotime($endDate)) }}</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4 font-bold">INVOICE NO</th>
                            <th class="py-3 px-4 font-bold">DATE</th>
                            <th class="py-3 px-4 font-bold">CUSTOMER</th>
                            <th class="py-3 px-4 font-bold">CHANNEL</th>
                            <th class="py-3 px-4 font-bold text-right">TOTAL (KES)</th>
                            <th class="py-3 px-4 font-bold text-right">PAID (KES)</th>
                            <th class="py-3 px-4 font-bold text-right">BALANCE DUE</th>
                            <th class="py-3 px-4 font-bold text-center">STATUS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($invoices as $inv)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-emerald-800">{{ $inv->invoice_no }}</td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $inv->invoice_date->format('d M Y') }}</td>
                                <td class="py-3.5 px-4 font-semibold text-slate-900">{{ $inv->customer->name ?? 'Walk-In Customer' }}</td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium {{ $inv->is_pos ? 'bg-purple-100 text-purple-800' : 'bg-slate-100 text-slate-700' }}">
                                        {{ $inv->is_pos ? 'POS Counter' : 'Wholesale Delivery' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right font-black text-slate-900">KES {{ number_format($inv->grand_total, 2) }}</td>
                                <td class="py-3.5 px-4 text-right font-bold text-emerald-700">KES {{ number_format($inv->paid_amount, 2) }}</td>
                                <td class="py-3.5 px-4 text-right font-black {{ $inv->balance_due > 0 ? 'text-rose-600' : 'text-slate-400' }}">
                                    KES {{ number_format($inv->balance_due, 2) }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ $inv->payment_status === 'Paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                        {{ $inv->payment_status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-slate-400">No sales transactions found in this date range.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-layouts.app>
