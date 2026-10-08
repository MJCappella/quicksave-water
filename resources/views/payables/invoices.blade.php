<x-layouts.app>
    <x-slot:title>Purchase Invoices & Bills</x-slot:title>

    <div class="space-y-6" x-data="{ openPinvModal: false }">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Purchase Invoices (Vendor Bills)</h1>
                <p class="text-xs text-slate-500 mt-0.5">Supplier bills booked against Goods Received Notes (GRN) and payment schedules.</p>
            </div>
            
            <button type="button" @click="openPinvModal = true"
                    class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Book Supplier Invoice</span>
            </button>
        </div>

        <!-- Invoices Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4 font-bold">INTERNAL BILL NO</th>
                            <th class="py-3 px-4 font-bold">SUPPLIER INVOICE #</th>
                            <th class="py-3 px-4 font-bold">VENDOR</th>
                            <th class="py-3 px-4 font-bold">BILL DATE</th>
                            <th class="py-3 px-4 font-bold">DUE DATE</th>
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
                                <td class="py-3.5 px-4 font-mono text-slate-700 font-semibold">{{ $inv->vendor_invoice_no }}</td>
                                <td class="py-3.5 px-4 font-semibold text-slate-900">{{ $inv->vendor->name ?? 'N/A' }}</td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $inv->invoice_date->format('d M Y') }}</td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $inv->due_date->format('d M Y') }}</td>
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
                                <td colspan="9" class="py-8 text-center text-slate-400">No purchase invoices booked yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($invoices->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $invoices->links() }}
                </div>
            @endif
        </div>

        <!-- MODAL: BOOK PURCHASE INVOICE -->
        <div x-cloak x-show="openPinvModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 border border-slate-100" @click.away="openPinvModal = false">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-black text-slate-900">Book Supplier Purchase Invoice</h3>
                    <button type="button" @click="openPinvModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                </div>

                <form method="POST" action="{{ route('payables.invoices.store') }}" class="space-y-3 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Vendor / Supplier</label>
                        <select name="vendor_id" required class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                            @foreach ($vendors as $v)
                                <option value="{{ $v->id }}">{{ $v->name }} (Balance: KES {{ number_format($v->current_balance) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Supplier's Bill / Invoice #</label>
                            <input type="text" name="vendor_invoice_no" required placeholder="e.g. INV-9024"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Linked G.R.N Ref (Optional)</label>
                            <select name="goods_received_note_id" class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                                <option value="">Direct Bill Entry</option>
                                @foreach ($grns as $grn)
                                    <option value="{{ $grn->id }}">{{ $grn->grn_no }} - {{ $grn->vendor->name ?? '' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Bill Date</label>
                            <input type="date" name="invoice_date" required value="{{ date('Y-m-d') }}"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Due Date</label>
                            <input type="date" name="due_date" required value="{{ date('Y-m-d', strtotime('+30 days')) }}"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                    </div>

                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 space-y-2">
                        <span class="block font-bold text-slate-800">Invoiced Line Item</span>
                        <div>
                            <label class="block text-[10px] text-slate-500 mb-1">Item Description</label>
                            <input type="text" name="items[0][description]" value="PET Preforms 500ml 18g Clear" required
                                   class="w-full rounded-lg border-slate-300 py-1.5 px-2 text-xs">
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[10px] text-slate-500 mb-1">Quantity</label>
                                <input type="number" step="0.01" name="items[0][quantity]" value="20000" required
                                       class="w-full rounded-lg border-slate-300 py-1.5 px-2 text-xs">
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-500 mb-1">Unit Price (KES)</label>
                                <input type="number" step="0.01" name="items[0][unit_price]" value="3.80" required
                                       class="w-full rounded-lg border-slate-300 py-1.5 px-2 text-xs">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Payment Instructions</label>
                        <textarea name="notes" rows="2" placeholder="Bank RTGS instructions..."
                                  class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="openPinvModal = false" class="px-4 py-2 rounded-xl text-slate-600 bg-slate-100 hover:bg-slate-200 font-bold">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black shadow-md shadow-emerald-600/20">
                            Book Bill to AP
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layouts.app>
