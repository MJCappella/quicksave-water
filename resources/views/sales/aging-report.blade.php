<x-layouts.app>
    <x-slot:title>Accounts Receivable (AR) Aging Report</x-slot:title>

    <div class="space-y-6">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">AR Debtors Aging Analysis</h1>
                <p class="text-xs text-slate-500 mt-0.5">Commercial receivables categorized by due dates: Current (0-30), 31-60, 61-90, and >90 days overdue.</p>
            </div>
            
            <button type="button" onclick="window.print()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 shadow-xs flex items-center gap-1.5">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Print AR Aging Statement</span>
            </button>
        </div>

        <!-- Aging Bucket Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">1 - 30 DAYS (CURRENT)</span>
                <div class="text-2xl font-black text-slate-900 mt-1">
                    KES {{ number_format($agingData->sum('days_30'), 2) }}
                </div>
                <p class="text-[11px] text-slate-500 font-medium mt-1">Within standard credit terms</p>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-[11px] font-bold text-teal-600 uppercase tracking-wider">31 - 60 DAYS</span>
                <div class="text-2xl font-black text-slate-900 mt-1">
                    KES {{ number_format($agingData->sum('days_60'), 2) }}
                </div>
                <p class="text-[11px] text-slate-500 font-medium mt-1">First reminder statements</p>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-[11px] font-bold text-amber-600 uppercase tracking-wider">61 - 90 DAYS</span>
                <div class="text-2xl font-black text-amber-700 mt-1">
                    KES {{ number_format($agingData->sum('days_90'), 2) }}
                </div>
                <p class="text-[11px] text-amber-600 font-medium mt-1">Escalated collection follow-up</p>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-[11px] font-bold text-rose-600 uppercase tracking-wider">> 90 DAYS (DELINQUENT)</span>
                <div class="text-2xl font-black text-rose-600 mt-1">
                    KES {{ number_format($agingData->sum('days_90_plus'), 2) }}
                </div>
                <p class="text-[11px] text-rose-600 font-medium mt-1">Credit hold / legal recovery</p>
            </div>
        </div>

        <!-- Aging Ledger Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Debtor Schedule & Aging Buckets</h2>
                    <p class="text-xs text-slate-400 mt-0.5">As of {{ date('d F Y') }}</p>
                </div>
                <div class="text-xs">
                    <span class="text-slate-400 font-semibold">Total AR Debt:</span>
                    <span class="font-black text-slate-900 text-sm ml-1">KES {{ number_format($totalAR, 2) }}</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4 font-bold">CUSTOMER CODE</th>
                            <th class="py-3 px-4 font-bold">CUSTOMER NAME</th>
                            <th class="py-3 px-4 font-bold">TYPE</th>
                            <th class="py-3 px-4 font-bold text-right">0 - 30 DAYS</th>
                            <th class="py-3 px-4 font-bold text-right">31 - 60 DAYS</th>
                            <th class="py-3 px-4 font-bold text-right">61 - 90 DAYS</th>
                            <th class="py-3 px-4 font-bold text-right">> 90 DAYS</th>
                            <th class="py-3 px-4 font-bold text-right">TOTAL BALANCE</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($agingData as $row)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-emerald-800">{{ $row['customer']->code }}</td>
                                <td class="py-3.5 px-4 font-semibold text-slate-900">
                                    {{ $row['customer']->name }}
                                    <div class="text-[10px] text-slate-400">Terms: {{ $row['customer']->payment_terms_days }} Days</div>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-700">
                                        {{ $row['customer']->customer_type }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right font-medium text-slate-700">
                                    KES {{ number_format($row['days_30'], 2) }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-medium text-slate-700">
                                    KES {{ number_format($row['days_60'], 2) }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-medium text-amber-700">
                                    KES {{ number_format($row['days_90'], 2) }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold text-rose-600">
                                    KES {{ number_format($row['days_90_plus'], 2) }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-black text-slate-900">
                                    KES {{ number_format($row['total_due'], 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-slate-400">No receivable balances outstanding.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-slate-50/80 font-black text-slate-900 border-t border-slate-200">
                        <tr>
                            <td colspan="3" class="py-3.5 px-4 uppercase text-slate-600">Total Receivables</td>
                            <td class="py-3.5 px-4 text-right">KES {{ number_format($agingData->sum('days_30'), 2) }}</td>
                            <td class="py-3.5 px-4 text-right">KES {{ number_format($agingData->sum('days_60'), 2) }}</td>
                            <td class="py-3.5 px-4 text-right text-amber-700">KES {{ number_format($agingData->sum('days_90'), 2) }}</td>
                            <td class="py-3.5 px-4 text-right text-rose-600">KES {{ number_format($agingData->sum('days_90_plus'), 2) }}</td>
                            <td class="py-3.5 px-4 text-right text-emerald-800 text-sm">KES {{ number_format($totalAR, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</x-layouts.app>
