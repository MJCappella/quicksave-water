@extends('layouts.app')

@section('title', 'Main Cashbook - QUICKSAVE AGENCIES LTD')
@section('page_title', 'Main Cashbook & Bank Journal')

@section('content')
<div class="space-y-6" x-data="{ showModal: false }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Commercial Bank & Main Cash Register</h2>
            <p class="text-xs text-gray-500 mt-1">Tracks business checking, corporate deposits, RTGS, direct EFT transfers, and contra bank movements.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-4 py-2 border border-gray-200 text-gray-700 bg-white hover:bg-gray-50 text-xs font-semibold rounded-xl flex items-center gap-1.5 shadow-sm transition">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print Cashbook
            </button>
            <button @click="showModal = true" class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-xl flex items-center gap-1.5 shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Bank Entry
            </button>
        </div>
    </div>

    <!-- Cards Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
        <div class="bg-gradient-to-br from-teal-800 to-emerald-900 text-white p-5 rounded-2xl shadow-sm">
            <div class="text-xs text-teal-200 uppercase tracking-wider font-semibold">Active Operating Balance</div>
            <div class="text-2xl font-black mt-2">KES {{ number_format($currentBalance, 2) }}</div>
            <div class="text-[11px] text-teal-300 mt-1">{{ $mainAccount?->bank_name ?? 'Equity Bank' }} (A/C: {{ $mainAccount?->account_number ?? '0112938472910' }})</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Bank Deposits</span>
                <span class="p-2 bg-emerald-50 text-emerald-600 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-emerald-700 mt-2">KES {{ number_format($totalInflow, 2) }}</div>
            <div class="text-[11px] text-gray-400 mt-1">Direct customer EFTs & receipts</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Payments (Withdrawals)</span>
                <span class="p-2 bg-rose-50 text-rose-600 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-rose-600 mt-2">KES {{ number_format($totalOutflow, 2) }}</div>
            <div class="text-[11px] text-gray-400 mt-1">Vendor settlements & payroll</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Managed Accounts</span>
                <span class="p-2 bg-blue-50 text-blue-600 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-gray-800 mt-2">{{ $bankAccounts->count() }} Accounts</div>
            <div class="text-[11px] text-gray-400 mt-1">Commercial & Mobile till vaults</div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-900 text-sm">Commercial Cashbook Entries ({{ $transactions->total() }})</h3>
            <span class="text-xs text-gray-500">Live ledger records</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-700">
                <thead class="bg-gray-50 text-gray-500 uppercase text-[10px] tracking-wider font-semibold">
                    <tr>
                        <th class="px-6 py-3">Txn No</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Account</th>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">Category</th>
                        <th class="px-6 py-3">Payee / Payer</th>
                        <th class="px-6 py-3">Reference / Slip</th>
                        <th class="px-6 py-3">Description</th>
                        <th class="px-6 py-3 text-right">Debit / Inflow</th>
                        <th class="px-6 py-3 text-right">Credit / Outflow</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($transactions as $txn)
                        <tr class="hover:bg-gray-50/70 transition">
                            <td class="px-6 py-4 font-bold text-emerald-700">{{ $txn->txn_no }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ \Carbon\Carbon::parse($txn->txn_date)->format('d M Y') }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $txn->account?->bank_name ?? 'Primary Bank' }}</td>
                            <td class="px-6 py-4">
                                @if($txn->type === 'INFLOW')
                                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded-full border border-emerald-200">INFLOW</span>
                                @elseif($txn->type === 'CONTRA')
                                    <span class="px-2 py-0.5 bg-blue-50 text-blue-700 text-[10px] font-bold rounded-full border border-blue-200">CONTRA</span>
                                @else
                                    <span class="px-2 py-0.5 bg-rose-50 text-rose-700 text-[10px] font-bold rounded-full border border-rose-200">OUTFLOW</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $txn->category }}</td>
                            <td class="px-6 py-4 text-gray-700">{{ $txn->payee_or_payer }}</td>
                            <td class="px-6 py-4 font-mono text-[11px] text-gray-500">{{ $txn->reference_no ?? '—' }}</td>
                            <td class="px-6 py-4 text-gray-600 max-w-xs truncate">{{ $txn->description }}</td>
                            <td class="px-6 py-4 text-right font-bold text-emerald-700">
                                {{ $txn->type === 'INFLOW' ? 'KES ' . number_format($txn->amount, 2) : '—' }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-rose-600">
                                {{ $txn->type !== 'INFLOW' ? 'KES ' . number_format($txn->amount, 2) : '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center text-gray-400">
                                No entries recorded in main cashbook yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $transactions->links() }}
        </div>
    </div>

    <!-- Modal Record Bank Entry -->
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4" style="display: none;" x-cloak>
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-gray-100" @click.away="showModal = false">
            <div class="flex justify-between items-center mb-5 pb-3 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900">Post Bank Journal Entry</h3>
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <form action="{{ route('banking.main-cash.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Bank Account *</label>
                    <select name="bank_account_id" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        @foreach($bankAccounts as $ba)
                            <option value="{{ $ba->id }}">{{ $ba->bank_name }} - {{ $ba->account_name }} ({{ $ba->account_number }}) - Bal: KES {{ number_format($ba->current_balance, 2) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Transaction Date *</label>
                        <input type="date" name="txn_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Entry Flow *</label>
                        <select name="type" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            <option value="INFLOW">Deposit / Inflow (Debit Bank)</option>
                            <option value="OUTFLOW">Payment / Outflow (Credit Bank)</option>
                            <option value="CONTRA">Contra Transfer (Internal Shift)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Category *</label>
                        <select name="category" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            <option value="Customer Direct Deposit">Customer Direct Deposit</option>
                            <option value="Supplier Settlement">Supplier Settlement</option>
                            <option value="Electricity / Water Utilities">Factory Electricity / Water Bills</option>
                            <option value="KEBS / Water Permit Levy">KEBS / Water Permit Levy</option>
                            <option value="Staff Payroll & Wages">Staff Payroll & Wages</option>
                            <option value="Loan Installment">Equipment Loan Installment</option>
                            <option value="Inter-Account Transfer">Inter-Account Transfer</option>
                            <option value="Bank Charges">Bank Maintenance & EFT Charges</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Amount (KES) *</label>
                        <input type="number" step="0.01" min="0.01" name="amount" required placeholder="0.00" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none font-bold">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Payee / Payer Entity *</label>
                    <input type="text" name="payee_or_payer" required placeholder="e.g. Kenya Power, Plastic Suppliers Ltd, Corporate Client" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Cheque / EFT / RTGS Ref</label>
                    <input type="text" name="reference_no" placeholder="e.g. EFT-982187 / CHQ-0021" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Description / Memo *</label>
                    <textarea name="description" rows="2" required placeholder="Transaction memo and purpose..." class="w-full px-3 py-2 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-gray-100">
                    <button type="button" @click="showModal = false" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-xl text-xs font-semibold hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold shadow-sm">Save Bank Entry</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
