<x-layouts.app>
    <x-slot:title>Payment Vouchers</x-slot:title>

    <div class="space-y-6" x-data="{ openVoucherModal: false }">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Payment Vouchers (PV)</h1>
                <p class="text-xs text-slate-500 mt-0.5">Authorization and settlement of accounts payable, utility bills, and raw material invoices.</p>
            </div>
            
            <button type="button" @click="openVoucherModal = true"
                    class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Authorize Payment Voucher</span>
            </button>
        </div>

        <!-- Vouchers Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4 font-bold">VOUCHER NO</th>
                            <th class="py-3 px-4 font-bold">PAYMENT DATE</th>
                            <th class="py-3 px-4 font-bold">VENDOR / PAYEE</th>
                            <th class="py-3 px-4 font-bold">INVOICE REF</th>
                            <th class="py-3 px-4 font-bold">PAYMENT METHOD</th>
                            <th class="py-3 px-4 font-bold">DISBURSED FROM</th>
                            <th class="py-3 px-4 font-bold text-right">AMOUNT (KES)</th>
                            <th class="py-3 px-4 font-bold">AUTHORIZING OFFICER</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($vouchers as $pv)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-emerald-800">{{ $pv->voucher_no }}</td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $pv->payment_date->format('d M Y') }}</td>
                                <td class="py-3.5 px-4 font-semibold text-slate-900">{{ $pv->vendor->name ?? 'N/A' }}</td>
                                <td class="py-3.5 px-4 font-mono text-slate-600">{{ $pv->invoice->invoice_no ?? 'On Account Settlement' }}</td>
                                <td class="py-3.5 px-4 text-slate-700 font-medium">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-800">
                                        {{ $pv->payment_method }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 truncate max-w-[150px]">{{ $pv->paid_from }}</td>
                                <td class="py-3.5 px-4 text-right font-black text-slate-900">
                                    KES {{ number_format($pv->amount, 2) }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $pv->approved_by }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-slate-400">No payment vouchers authorized yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($vouchers->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $vouchers->links() }}
                </div>
            @endif
        </div>

        <!-- MODAL: ISSUE PAYMENT VOUCHER -->
        <div x-cloak x-show="openVoucherModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 border border-slate-100" @click.away="openVoucherModal = false">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-black text-slate-900">Authorize Payment Voucher</h3>
                    <button type="button" @click="openVoucherModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                </div>

                <form method="POST" action="{{ route('payables.vouchers.store') }}" class="space-y-3 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Payee Vendor</label>
                        <select name="vendor_id" required class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                            @foreach ($vendors as $v)
                                <option value="{{ $v->id }}">{{ $v->name }} (Balance Owed: KES {{ number_format($v->current_balance) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Supplier Bill (Optional)</label>
                            <select name="purchase_invoice_id" class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                                <option value="">On Account Advance / Direct Payment</option>
                                @foreach ($invoices as $inv)
                                    <option value="{{ $inv->id }}">{{ $inv->invoice_no }} (Due: KES {{ number_format($inv->balance_due) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Payment Date</label>
                            <input type="date" name="payment_date" required value="{{ date('Y-m-d') }}"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Payment Amount (KES)</label>
                            <input type="number" step="0.01" name="amount" required placeholder="e.g. 114000"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Payment Method</label>
                            <select name="payment_method" class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                                <option value="Bank Transfer">Bank Wire / RTGS</option>
                                <option value="Cheque">Bank Cheque</option>
                                <option value="Mpesa">M-Pesa Corporate B2B</option>
                                <option value="Cash">Cashier Safe Float</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Cheque / Transfer Ref #</label>
                            <input type="text" name="reference_no" placeholder="e.g. CHQ-9014 / KCB-892"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Paid From Account</label>
                            <input type="text" name="paid_from" value="KCB Main Operating Account" required
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Payment Narration</label>
                        <textarea name="notes" rows="2" placeholder="Full settlement of raw material delivery..."
                                  class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="openVoucherModal = false" class="px-4 py-2 rounded-xl text-slate-600 bg-slate-100 hover:bg-slate-200 font-bold">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black shadow-md shadow-emerald-600/20">
                            Authorize Voucher
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layouts.app>
