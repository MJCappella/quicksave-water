@extends('layouts.app')

@section('title', 'M-Pesa Paybill & Till Register - QUICKSAVE AGENCIES LTD')
@section('page_title', 'M-Pesa Paybill & Buy Goods Register')

@section('content')
<div class="space-y-6" x-data="{ 
    allocateModal: false, 
    currentTxnId: null, 
    currentTxnCode: '', 
    currentAmount: 0,
    allocType: 'SalesInvoice'
}">
    <!-- Top Action & Overview -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Safaricom M-Pesa Transactions & Settlements</h2>
            <p class="text-xs text-gray-500 mt-1">Real-time incoming C2B Paybill payments and retail counter Till / Buy Goods collections.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-4 py-2 border border-gray-200 text-gray-700 bg-white hover:bg-gray-50 text-xs font-semibold rounded-xl flex items-center gap-1.5 shadow-sm transition">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print Statement
            </button>
        </div>
    </div>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
        <div class="bg-gradient-to-br from-emerald-700 to-teal-800 text-white p-5 rounded-2xl shadow-sm">
            <div class="text-xs text-emerald-200 uppercase tracking-wider font-semibold">Total M-Pesa Collected</div>
            <div class="text-2xl font-black mt-2">KES {{ number_format($totalCollected, 2) }}</div>
            <div class="text-[11px] text-emerald-300 mt-1">Direct Safaricom settlements</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Unallocated Entries</span>
                <span class="p-2 bg-amber-50 text-amber-600 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-amber-600 mt-2">{{ $unallocatedCount }}</div>
            <div class="text-[11px] text-amber-600 mt-1">Pending invoice/customer pairing</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Unallocated Value</span>
                <span class="p-2 bg-amber-50 text-amber-600 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-amber-600 mt-2">KES {{ number_format($unallocatedAmount, 2) }}</div>
            <div class="text-[11px] text-gray-400 mt-1">Awaiting reconciliation</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Active Channels</span>
                <span class="p-2 bg-emerald-50 text-emerald-600 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </span>
            </div>
            <div class="text-base font-bold text-gray-800 mt-2">Paybill: 890123 / Till: 549812</div>
            <div class="text-[11px] text-gray-400 mt-1">Automated C2B API Integration</div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="text-xs font-semibold text-gray-500">Filter Channel:</span>
            <a href="{{ route('banking.mpesa') }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold {{ empty($selectedType) ? 'bg-emerald-700 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">All Channels</a>
            <a href="{{ route('banking.mpesa', ['type' => 'Paybill']) }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold {{ $selectedType === 'Paybill' ? 'bg-emerald-700 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">Paybill (890123)</a>
            <a href="{{ route('banking.mpesa', ['type' => 'Till']) }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold {{ $selectedType === 'Till' ? 'bg-emerald-700 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">Retail Till (549812)</a>
        </div>
        <div class="text-xs text-gray-400">
            Showing {{ $transactions->count() }} of {{ $transactions->total() }} records
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-900 text-sm">M-Pesa C2B Statement Ledger</h3>
            <span class="text-xs text-gray-500">Instant SMS confirmation receipt matching</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-700">
                <thead class="bg-gray-50 text-gray-500 uppercase text-[10px] tracking-wider font-semibold">
                    <tr>
                        <th class="px-6 py-3">Receipt Code</th>
                        <th class="px-6 py-3">Date & Time</th>
                        <th class="px-6 py-3">Sender Name</th>
                        <th class="px-6 py-3">Phone Number</th>
                        <th class="px-6 py-3">Channel / Type</th>
                        <th class="px-6 py-3">Bill Ref / Account</th>
                        <th class="px-6 py-3 text-right">Amount (KES)</th>
                        <th class="px-6 py-3 text-center">Status</th>
                        <th class="px-6 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($transactions as $txn)
                        <tr class="hover:bg-gray-50/70 transition">
                            <td class="px-6 py-4 font-mono font-bold text-emerald-800">{{ $txn->mpesa_receipt_number }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ \Carbon\Carbon::parse($txn->transaction_time)->format('d M Y, H:i') }}</td>
                            <td class="px-6 py-4 font-semibold text-gray-900">{{ $txn->sender_name }}</td>
                            <td class="px-6 py-4 text-gray-500 font-mono">{{ $txn->sender_phone }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $txn->type === 'Paybill' ? 'bg-purple-50 text-purple-700 border border-purple-200' : 'bg-teal-50 text-teal-700 border border-teal-200' }}">
                                    {{ $txn->type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-700 font-mono text-[11px]">{{ $txn->bill_ref_number ?? 'POS Counter' }}</td>
                            <td class="px-6 py-4 text-right font-black text-emerald-700 text-sm">KES {{ number_format($txn->amount, 2) }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($txn->status === 'Allocated')
                                    <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded-lg border border-emerald-200">ALLOCATED</span>
                                @else
                                    <span class="px-2.5 py-1 bg-amber-50 text-amber-700 text-[10px] font-bold rounded-lg border border-amber-200">UNALLOCATED</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($txn->status === 'Unallocated')
                                    <button 
                                        @click="
                                            currentTxnId = {{ $txn->id }}; 
                                            currentTxnCode = '{{ $txn->mpesa_receipt_number }}'; 
                                            currentAmount = '{{ number_format($txn->amount, 2) }}';
                                            allocateModal = true;
                                        " 
                                        class="px-3 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                                        Allocate
                                    </button>
                                @else
                                    <span class="text-xs text-gray-400 font-medium">Linked ({{ $txn->allocated_to_type }})</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-gray-400">
                                No M-Pesa transactions found.
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

    <!-- Allocate Modal -->
    <div x-show="allocateModal" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4" style="display: none;" x-cloak>
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-gray-100" @click.away="allocateModal = false">
            <div class="flex justify-between items-center mb-4 pb-3 border-b border-gray-100">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Allocate M-Pesa Payment</h3>
                    <p class="text-xs text-gray-500">Pair receipt <span class="font-mono font-bold text-emerald-700" x-text="currentTxnCode"></span> for KES <span class="font-bold text-gray-900" x-text="currentAmount"></span></p>
                </div>
                <button @click="allocateModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <form :action="'{{ url('/banking/mpesa') }}/' + currentTxnId + '/allocate'" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Allocate Destination *</label>
                    <select name="allocated_to_type" x-model="allocType" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <option value="SalesInvoice">Direct to Sales Invoice</option>
                        <option value="Customer">Customer Account Credit (Prepayment)</option>
                    </select>
                </div>

                <div x-show="allocType === 'SalesInvoice'">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Select Unpaid Sales Invoice *</label>
                    <select name="allocated_to_id" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        @foreach($invoices as $inv)
                            <option value="{{ $inv->id }}">{{ $inv->invoice_no }} - {{ $inv->customer?->name }} (Due: KES {{ number_format($inv->balance_due, 2) }})</option>
                        @endforeach
                    </select>
                </div>

                <div x-show="allocType === 'Customer'" style="display: none;">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Select Customer Account *</label>
                    <select name="allocated_to_id" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} (Bal: KES {{ number_format($c->current_balance, 2) }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="allocateModal = false" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-xl text-xs font-semibold hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold shadow-sm">Confirm Allocation</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
