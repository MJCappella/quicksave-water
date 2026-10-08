@extends('layouts.app')

@section('title', 'Petty Cashbook - QUICKSAVE AGENCIES LTD')
@section('page_title', 'Petty Cashbook Management')

@section('content')
<div class="space-y-6" x-data="{ showModal: false }">
    <!-- Top Action & Overview -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Petty Cashbook Registry</h2>
            <p class="text-xs text-gray-500 mt-1">Imprest float management, office sundries, local delivery fuel, and staff disbursements.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-4 py-2 border border-gray-200 text-gray-700 bg-white hover:bg-gray-50 text-xs font-semibold rounded-xl flex items-center gap-1.5 shadow-sm transition">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print Cashbook
            </button>
            <button @click="showModal = true" class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-xl flex items-center gap-1.5 shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Record Cash Transaction
            </button>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
        <div class="bg-gradient-to-br from-emerald-800 to-teal-900 text-white p-5 rounded-2xl shadow-sm">
            <div class="text-xs text-emerald-200 uppercase tracking-wider font-semibold">Petty Cash Float Balance</div>
            <div class="text-2xl font-black mt-2">KES {{ number_format($currentBalance, 2) }}</div>
            <div class="text-[11px] text-emerald-300 mt-1">Float Limit: KES 100,000.00</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Inflows (Reimbursements)</span>
                <span class="p-2 bg-emerald-50 text-emerald-600 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-emerald-700 mt-2">KES {{ number_format($totalInflow, 2) }}</div>
            <div class="text-[11px] text-gray-400 mt-1">Imprest top-ups & cash receipts</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Outflows (Expenses)</span>
                <span class="p-2 bg-rose-50 text-rose-600 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-rose-600 mt-2">KES {{ number_format($totalOutflow, 2) }}</div>
            <div class="text-[11px] text-gray-400 mt-1">Disbursements recorded</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Account Status</span>
                <span class="p-2 bg-teal-50 text-teal-600 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="text-base font-bold text-gray-800 mt-2">Active Imprest System</div>
            <div class="text-[11px] text-gray-500 mt-1">{{ $pettyAccount?->bank_name ?? 'Cash in Hand Vault' }}</div>
        </div>
    </div>

    <!-- Cashbook Transactions Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-900 text-sm">Petty Cash Vouchers & Entries</h3>
            <span class="text-xs text-gray-500">Live double-entry postings</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-700">
                <thead class="bg-gray-50 text-gray-500 uppercase text-[10px] tracking-wider font-semibold">
                    <tr>
                        <th class="px-6 py-3">Txn No</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">Category</th>
                        <th class="px-6 py-3">Payee / Payer</th>
                        <th class="px-6 py-3">Reference / Receipt</th>
                        <th class="px-6 py-3">Description</th>
                        <th class="px-6 py-3 text-right">Inflow (+)</th>
                        <th class="px-6 py-3 text-right">Outflow (-)</th>
                        <th class="px-6 py-3">Authorized By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($transactions as $txn)
                        <tr class="hover:bg-gray-50/70 transition">
                            <td class="px-6 py-4 font-bold text-emerald-700">{{ $txn->txn_no }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ \Carbon\Carbon::parse($txn->txn_date)->format('d M Y') }}</td>
                            <td class="px-6 py-4">
                                @if($txn->type === 'INFLOW')
                                    <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded-full border border-emerald-200">INFLOW</span>
                                @else
                                    <span class="px-2.5 py-0.5 bg-rose-50 text-rose-700 text-[10px] font-bold rounded-full border border-rose-200">OUTFLOW</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-semibold text-gray-900">{{ $txn->category }}</td>
                            <td class="px-6 py-4 text-gray-700">{{ $txn->payee_or_payer }}</td>
                            <td class="px-6 py-4 text-gray-500 font-mono text-[11px]">{{ $txn->reference_no ?? '—' }}</td>
                            <td class="px-6 py-4 text-gray-600 max-w-xs truncate">{{ $txn->description }}</td>
                            <td class="px-6 py-4 text-right font-bold text-emerald-700">
                                {{ $txn->type === 'INFLOW' ? 'KES ' . number_format($txn->amount, 2) : '—' }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-rose-600">
                                {{ $txn->type === 'OUTFLOW' ? 'KES ' . number_format($txn->amount, 2) : '—' }}
                            </td>
                            <td class="px-6 py-4 text-gray-500">{{ $txn->approved_by ?? 'Audited' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center text-gray-400">
                                No transactions recorded in petty cashbook yet.
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

    <!-- Modal Record Petty Cash -->
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4" style="display: none;" x-cloak>
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-gray-100" @click.away="showModal = false">
            <div class="flex justify-between items-center mb-5 pb-3 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900">Record Petty Cash Transaction</h3>
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <form action="{{ route('banking.petty-cash.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Transaction Date *</label>
                        <input type="date" name="txn_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Type *</label>
                        <select name="type" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            <option value="OUTFLOW">Outflow (Expense / Payment)</option>
                            <option value="INFLOW">Inflow (Float Reimbursement / Cash In)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Category *</label>
                        <select name="category" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            <option value="Vehicle Fuel & Tolls">Vehicle Fuel & Tolls</option>
                            <option value="Office Sundries">Office Sundries & Tea</option>
                            <option value="Repairs & Maintenance">Repairs & Minor Maintenance</option>
                            <option value="Staff Daily Allowances">Staff Daily Allowances</option>
                            <option value="Loading & Offloading Casuals">Loading Casuals</option>
                            <option value="Float Replenishment">Float Replenishment</option>
                            <option value="Other">Other Expenses</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Amount (KES) *</label>
                        <input type="number" step="0.01" min="0.01" name="amount" required placeholder="0.00" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none font-bold">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Payee / Received From *</label>
                    <input type="text" name="payee_or_payer" required placeholder="e.g. Total Petrol Station, Casual Crew, Petty Cashier" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Reference / Receipt No</label>
                    <input type="text" name="reference_no" placeholder="e.g. REC-8921 / ETR" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Description / Narration *</label>
                    <textarea name="description" rows="2" required placeholder="Purpose of petty cash disbursement..." class="w-full px-3 py-2 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-gray-100">
                    <button type="button" @click="showModal = false" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-xl text-xs font-semibold hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold shadow-sm">Save Transaction</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
