<x-layouts.app>
    <x-slot:title>Customer Receipts & Collections</x-slot:title>

    <div class="space-y-6" x-data="{ openReceiptModal: false }">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Customer & Direct Receipts</h1>
                <p class="text-xs text-slate-500 mt-0.5">Recording invoice settlements, direct counter collections, M-Pesa, and bank deposits.</p>
            </div>
            
            <button type="button" @click="openReceiptModal = true"
                    class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Receive Customer Payment</span>
            </button>
        </div>

        <!-- Receipts Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4 font-bold">RECEIPT NO</th>
                            <th class="py-3 px-4 font-bold">DATE</th>
                            <th class="py-3 px-4 font-bold">CUSTOMER</th>
                            <th class="py-3 px-4 font-bold">INVOICE REF</th>
                            <th class="py-3 px-4 font-bold">PAYMENT METHOD</th>
                            <th class="py-3 px-4 font-bold">REF / M-PESA CODE</th>
                            <th class="py-3 px-4 font-bold text-right">AMOUNT (KES)</th>
                            <th class="py-3 px-4 font-bold">CASHIER / OFFICER</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($receipts as $rcp)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-emerald-800">{{ $rcp->receipt_no }}</td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $rcp->receipt_date->format('d M Y') }}</td>
                                <td class="py-3.5 px-4 font-semibold text-slate-900">{{ $rcp->customer->name ?? 'Walk-In Customer' }}</td>
                                <td class="py-3.5 px-4 font-mono text-slate-600">{{ $rcp->invoice->invoice_no ?? 'Direct Settlement' }}</td>
                                <td class="py-3.5 px-4 text-slate-700 font-medium">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-800">
                                        {{ $rcp->payment_method }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 font-mono font-bold text-slate-800">{{ $rcp->reference_no ?? 'N/A' }}</td>
                                <td class="py-3.5 px-4 text-right font-black text-emerald-700">
                                    KES {{ number_format($rcp->amount, 2) }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $rcp->received_by }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-slate-400">No customer receipts logged.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($receipts->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $receipts->links() }}
                </div>
            @endif
        </div>

        <!-- MODAL: RECEIVE CUSTOMER PAYMENT -->
        <div x-cloak x-show="openReceiptModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 border border-slate-100" @click.away="openReceiptModal = false">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-black text-slate-900">Record Customer Receipt</h3>
                    <button type="button" @click="openReceiptModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                </div>

                <form method="POST" action="{{ route('sales.receipts.store') }}" class="space-y-3 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Customer Account</label>
                        <select name="customer_id" required class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                            @foreach ($customers as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} (Balance: KES {{ number_format($c->current_balance) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Outstanding Invoice (Optional)</label>
                            <select name="sales_invoice_id" class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                                <option value="">On Account / Direct Settlement</option>
                                @foreach ($invoices as $inv)
                                    <option value="{{ $inv->id }}">{{ $inv->invoice_no }} (Due: KES {{ number_format($inv->balance_due) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Receipt Date</label>
                            <input type="date" name="receipt_date" required value="{{ date('Y-m-d') }}"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Amount Received (KES)</label>
                            <input type="number" step="0.01" name="amount" required placeholder="e.g. 50000"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Payment Method</label>
                            <select name="payment_method" class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                                <option value="Mpesa">M-Pesa (Till / Paybill)</option>
                                <option value="Bank Transfer">Bank RTGS / EFT</option>
                                <option value="Cheque">Bank Cheque</option>
                                <option value="Cash">Cash</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Confirmation / Cheque Ref</label>
                            <input type="text" name="reference_no" placeholder="e.g. QAL8392019 / CHQ-0021"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Receipt Classification</label>
                            <select name="receipt_type" class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                                <option value="Invoice Payment">Invoice Payment</option>
                                <option value="Direct Receipt">Direct Receipt</option>
                                <option value="Customer Deposit">Customer Deposit</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Payment Description</label>
                        <textarea name="notes" rows="2" placeholder="Full payment against delivered invoice..."
                                  class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="openReceiptModal = false" class="px-4 py-2 rounded-xl text-slate-600 bg-slate-100 hover:bg-slate-200 font-bold">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black shadow-md shadow-emerald-600/20">
                            Issue Official Receipt
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layouts.app>
