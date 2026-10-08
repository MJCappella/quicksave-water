@extends('layouts.app')

@section('title', 'Bank Reconciliation - QUICKSAVE AGENCIES LTD')
@section('page_title', 'Bank Reconciliation Statements')

@section('content')
<div class="space-y-6" x-data="{ showModal: false }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Bank Reconciliation Worksheet</h2>
            <p class="text-xs text-gray-500 mt-1">Reconcile general ledger book balance against official bank ending statement balances, uncredited lodgements, and unpresented cheques.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-4 py-2 border border-gray-200 text-gray-700 bg-white hover:bg-gray-50 text-xs font-semibold rounded-xl flex items-center gap-1.5 shadow-sm transition">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print Statement
            </button>
            <button @click="showModal = true" class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-xl flex items-center gap-1.5 shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Perform Bank Reconciliation
            </button>
        </div>
    </div>

    @if($reconciliation)
    <!-- Active Reconciliation Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Statement Ending Balance</span>
            <div class="text-2xl font-black text-gray-900 mt-2">KES {{ number_format($reconciliation->statement_ending_balance, 2) }}</div>
            <div class="text-[11px] text-gray-400 mt-1">As per bank statement ({{ \Carbon\Carbon::parse($reconciliation->statement_date)->format('d M Y') }})</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Cashbook (Book) Balance</span>
            <div class="text-2xl font-black text-gray-900 mt-2">KES {{ number_format($reconciliation->book_balance, 2) }}</div>
            <div class="text-[11px] text-gray-400 mt-1">General Ledger balance</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Variance / Difference</span>
            <div class="text-2xl font-black {{ abs($reconciliation->difference) < 0.01 ? 'text-emerald-700' : 'text-amber-600' }} mt-2">
                KES {{ number_format($reconciliation->difference, 2) }}
            </div>
            <div class="text-[11px] {{ abs($reconciliation->difference) < 0.01 ? 'text-emerald-600' : 'text-amber-600' }} mt-1">
                {{ abs($reconciliation->difference) < 0.01 ? 'Exact Match (Balanced)' : 'Variance needs investigation' }}
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Status & Reference</span>
            <div class="mt-2 flex items-center gap-2">
                @if($reconciliation->status === 'Reconciled')
                    <span class="px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-lg border border-emerald-200">RECONCILED</span>
                @else
                    <span class="px-3 py-1 bg-amber-50 text-amber-700 text-xs font-bold rounded-lg border border-amber-200">IN PROGRESS</span>
                @endif
            </div>
            <div class="text-[11px] text-gray-400 mt-1 font-mono">{{ $reconciliation->reconciliation_no }}</div>
        </div>
    </div>

    <!-- Detailed Worksheet Comparison -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-6">
        <div class="flex justify-between items-center border-b border-gray-100 pb-4">
            <div>
                <h3 class="text-base font-bold text-gray-900">Reconciliation Statement Report: {{ $reconciliation->reconciliation_no }}</h3>
                <p class="text-xs text-gray-500">Bank Account: {{ $reconciliation->bankAccount?->bank_name }} - {{ $reconciliation->bankAccount?->account_number }}</p>
            </div>
            <div class="text-xs text-gray-400">
                Reconciled by: <span class="font-semibold text-gray-700">{{ $reconciliation->reconciled_by }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Left Side: Bank to Book Reconciliation -->
            <div class="space-y-4">
                <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-800 border-b pb-2">Balance Computation</h4>
                <div class="space-y-3 text-xs">
                    <div class="flex justify-between py-1.5 border-b border-gray-50">
                        <span class="text-gray-600">Balance as per Bank Statement</span>
                        <span class="font-bold text-gray-900">KES {{ number_format($reconciliation->statement_ending_balance, 2) }}</span>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-gray-50 text-emerald-700">
                        <span>Add: Deposits in Transit / Uncredited Lodgements</span>
                        <span class="font-semibold">+ KES 0.00</span>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-gray-50 text-rose-600">
                        <span>Less: Outstanding / Unpresented Cheques</span>
                        <span class="font-semibold">- KES 0.00</span>
                    </div>
                    <div class="flex justify-between py-2 border-t border-gray-200 font-bold bg-gray-50 px-3 rounded-lg">
                        <span class="text-gray-900">Adjusted Bank Balance</span>
                        <span class="text-emerald-800 font-black">KES {{ number_format($reconciliation->cleared_balance, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Right Side: Book Side -->
            <div class="space-y-4">
                <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-800 border-b pb-2">Book Balance Comparison</h4>
                <div class="space-y-3 text-xs">
                    <div class="flex justify-between py-1.5 border-b border-gray-50">
                        <span class="text-gray-600">Balance as per General Ledger (Cashbook)</span>
                        <span class="font-bold text-gray-900">KES {{ number_format($reconciliation->book_balance, 2) }}</span>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-gray-50 text-gray-500">
                        <span>Bank Charges / Standing Orders Deducted</span>
                        <span>KES 0.00</span>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-gray-50 text-gray-500">
                        <span>Direct Customer Credits Not in Cashbook</span>
                        <span>KES 0.00</span>
                    </div>
                    <div class="flex justify-between py-2 border-t border-gray-200 font-bold bg-gray-50 px-3 rounded-lg">
                        <span class="text-gray-900">Net Variance</span>
                        <span class="{{ abs($reconciliation->difference) < 0.01 ? 'text-emerald-700' : 'text-amber-600' }} font-black">
                            KES {{ number_format($reconciliation->difference, 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        @if($reconciliation->notes)
            <div class="p-4 bg-gray-50 rounded-xl text-xs text-gray-600">
                <span class="font-bold text-gray-800">Reconciliation Notes:</span> {{ $reconciliation->notes }}
            </div>
        @endif
    </div>
    @else
        <div class="bg-white p-12 text-center rounded-2xl border border-gray-100 shadow-sm text-gray-500">
            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <p class="font-medium">No reconciliation statements found yet.</p>
            <p class="text-xs text-gray-400 mt-1">Click "Perform Bank Reconciliation" above to start monthly or weekly matching.</p>
        </div>
    @endif

    <!-- Perform Reconciliation Modal -->
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4" style="display: none;" x-cloak>
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-gray-100" @click.away="showModal = false">
            <div class="flex justify-between items-center mb-4 pb-3 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900">Perform Bank Reconciliation</h3>
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <form action="{{ route('banking.reconciliation.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Select Bank Account *</label>
                    <select name="bank_account_id" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        @foreach($bankAccounts as $ba)
                            <option value="{{ $ba->id }}">{{ $ba->bank_name }} - {{ $ba->account_name }} (GL Book Bal: KES {{ number_format($ba->current_balance, 2) }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Statement Date *</label>
                        <input type="date" name="statement_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Statement Ending Bal (KES) *</label>
                        <input type="number" step="0.01" name="statement_ending_balance" required placeholder="0.00" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none font-bold">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Reconciliation Findings & Notes</label>
                    <textarea name="notes" rows="3" placeholder="Explain outstanding checks, deposits in transit, or matched bank fee slips..." class="w-full px-3 py-2 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-gray-100">
                    <button type="button" @click="showModal = false" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-xl text-xs font-semibold hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold shadow-sm">Save & Compute Reconciliation</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
