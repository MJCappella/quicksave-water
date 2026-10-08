@extends('layouts.app')

@section('title', 'Purchase & Payables Reports - QUICKSAVE AGENCIES LTD')
@section('page_title', 'Purchase & Payables Reports')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Procurement & Accounts Payable Summary</h2>
            <p class="text-xs text-gray-500 mt-1">Comprehensive breakdown of purchase invoices, supplier settlements, and outstanding liabilities.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-4 py-2 border border-gray-200 text-gray-700 bg-white hover:bg-gray-50 text-xs font-semibold rounded-xl flex items-center gap-1.5 shadow-sm transition">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print Report
            </button>
            <a href="{{ route('payables.aging') }}" class="px-4 py-2 bg-emerald-50 text-emerald-800 hover:bg-emerald-100 text-xs font-semibold rounded-xl flex items-center gap-1.5 transition">
                View AP Aging &rarr;
            </a>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
        <form method="GET" action="{{ route('payables.reports') }}" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-2 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-2 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>
            <button type="submit" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-xl transition shadow-sm">
                Apply Date Filter
            </button>
            <a href="{{ route('payables.reports') }}" class="px-4 py-2 text-xs text-gray-500 hover:text-gray-800">
                Reset
            </a>
        </form>
    </div>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Purchases</span>
                <span class="p-2 bg-emerald-50 text-emerald-600 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-gray-900 mt-2">KES {{ number_format($totalPurchases, 2) }}</div>
            <div class="text-[11px] text-gray-400 mt-1">Period: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Settled (Paid)</span>
                <span class="p-2 bg-teal-50 text-teal-600 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-teal-700 mt-2">KES {{ number_format($totalPaid, 2) }}</div>
            <div class="text-[11px] text-teal-600 mt-1">Cleared to vendors</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Outstanding Balance Due</span>
                <span class="p-2 bg-amber-50 text-amber-600 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-amber-600 mt-2">KES {{ number_format($totalOwed, 2) }}</div>
            <div class="text-[11px] text-amber-600 mt-1">Unsettled vendor liability</div>
        </div>
    </div>

    <!-- Purchase Invoices Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-900 text-sm">Purchase Invoices Breakdown ({{ $invoices->count() }})</h3>
            <span class="text-xs text-gray-500">Sorted by invoice date (newest first)</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-700">
                <thead class="bg-gray-50 text-gray-500 uppercase text-[10px] tracking-wider font-semibold">
                    <tr>
                        <th class="px-6 py-3">Invoice No</th>
                        <th class="px-6 py-3">Vendor / Supplier</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Due Date</th>
                        <th class="px-6 py-3 text-right">Subtotal</th>
                        <th class="px-6 py-3 text-right">Tax (VAT)</th>
                        <th class="px-6 py-3 text-right">Grand Total</th>
                        <th class="px-6 py-3 text-right">Paid</th>
                        <th class="px-6 py-3 text-right">Balance Due</th>
                        <th class="px-6 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($invoices as $inv)
                        <tr class="hover:bg-gray-50/70 transition">
                            <td class="px-6 py-4 font-bold text-emerald-700">
                                {{ $inv->invoice_no }}
                                @if($inv->vendor_invoice_ref)
                                    <div class="text-[10px] text-gray-400 font-normal">Ref: {{ $inv->vendor_invoice_ref }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-900">{{ $inv->vendor?->company_name }}</div>
                                <div class="text-[11px] text-gray-400">{{ $inv->vendor?->contact_person }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ \Carbon\Carbon::parse($inv->invoice_date)->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ \Carbon\Carbon::parse($inv->due_date)->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-right">KES {{ number_format($inv->subtotal, 2) }}</td>
                            <td class="px-6 py-4 text-right">KES {{ number_format($inv->tax_amount, 2) }}</td>
                            <td class="px-6 py-4 text-right font-bold text-gray-900">KES {{ number_format($inv->grand_total, 2) }}</td>
                            <td class="px-6 py-4 text-right text-teal-700 font-medium">KES {{ number_format($inv->paid_amount, 2) }}</td>
                            <td class="px-6 py-4 text-right font-bold {{ $inv->balance_due > 0 ? 'text-amber-600' : 'text-gray-400' }}">
                                KES {{ number_format($inv->balance_due, 2) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($inv->payment_status === 'Paid')
                                    <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded-lg border border-emerald-200">PAID</span>
                                @elseif($inv->payment_status === 'Partial')
                                    <span class="px-2.5 py-1 bg-amber-50 text-amber-700 text-[10px] font-bold rounded-lg border border-amber-200">PARTIAL</span>
                                @else
                                    <span class="px-2.5 py-1 bg-rose-50 text-rose-700 text-[10px] font-bold rounded-lg border border-rose-200">UNPAID</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center text-gray-400">
                                No purchase invoices found for this date range.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($invoices->count() > 0)
                    <tfoot class="bg-gray-50/80 font-bold text-gray-900 border-t-2 border-gray-200">
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-right uppercase text-[11px] tracking-wider text-gray-600">Total:</td>
                            <td class="px-6 py-4 text-right">KES {{ number_format($totalPurchases, 2) }}</td>
                            <td class="px-6 py-4 text-right text-teal-700">KES {{ number_format($totalPaid, 2) }}</td>
                            <td class="px-6 py-4 text-right text-amber-600">KES {{ number_format($totalOwed, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
