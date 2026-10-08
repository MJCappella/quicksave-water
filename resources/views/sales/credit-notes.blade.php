<x-layouts.app>
    <x-slot:title>Credit Notes</x-slot:title>

    <div class="space-y-6" x-data="{ openCreditModal: false }">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Credit Notes</h1>
                <p class="text-xs text-slate-500 mt-0.5">Sales returns, damaged seal replacements, and invoice billing adjustments.</p>
            </div>
            
            <button type="button" @click="openCreditModal = true"
                    class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Issue Credit Note</span>
            </button>
        </div>

        <!-- Credit Notes Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4 font-bold">CREDIT NOTE NO</th>
                            <th class="py-3 px-4 font-bold">DATE</th>
                            <th class="py-3 px-4 font-bold">CUSTOMER</th>
                            <th class="py-3 px-4 font-bold">INVOICE REF</th>
                            <th class="py-3 px-4 font-bold">REASON FOR RETURN</th>
                            <th class="py-3 px-4 font-bold text-right">CREDIT AMOUNT (KES)</th>
                            <th class="py-3 px-4 font-bold text-center">STATUS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($creditNotes as $cn)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-emerald-800">{{ $cn->credit_note_no }}</td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $cn->credit_date->format('d M Y') }}</td>
                                <td class="py-3.5 px-4 font-semibold text-slate-900">{{ $cn->customer->name ?? 'N/A' }}</td>
                                <td class="py-3.5 px-4 font-mono text-slate-600">{{ $cn->invoice->invoice_no ?? 'Direct Account Credit' }}</td>
                                <td class="py-3.5 px-4 text-slate-700">
                                    <div class="font-medium text-slate-900">{{ $cn->reason }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $cn->notes }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-right font-black text-rose-600">
                                    KES {{ number_format($cn->amount, 2) }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        {{ $cn->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400">No credit notes issued.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($creditNotes->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $creditNotes->links() }}
                </div>
            @endif
        </div>

        <!-- MODAL: ISSUE CREDIT NOTE -->
        <div x-cloak x-show="openCreditModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 border border-slate-100" @click.away="openCreditModal = false">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-black text-slate-900">Issue Credit Note</h3>
                    <button type="button" @click="openCreditModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                </div>

                <form method="POST" action="{{ route('sales.credit-notes.store') }}" class="space-y-3 text-xs">
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
                            <label class="block font-bold text-slate-700 mb-1">Invoice Reference (Optional)</label>
                            <select name="sales_invoice_id" class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                                <option value="">Direct Account Adjustment</option>
                                @foreach ($invoices as $inv)
                                    <option value="{{ $inv->id }}">{{ $inv->invoice_no }} (KES {{ number_format($inv->grand_total) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Credit Date</label>
                            <input type="date" name="credit_date" required value="{{ date('Y-m-d') }}"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Credit Amount (KES)</label>
                        <input type="number" step="0.01" name="amount" required placeholder="e.g. 1500"
                               class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Reason for Return</label>
                        <select name="reason" class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                            <option value="Returned Damaged Seal Bottles">Returned Damaged Seal Bottles</option>
                            <option value="Defective 5-Gallon Dispenser Cap">Defective 5-Gallon Dispenser Cap</option>
                            <option value="Overbilled Price Correction">Overbilled Price Correction</option>
                            <option value="Promotion / Trade Rebate">Promotion / Trade Rebate</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Audit Notes</label>
                        <textarea name="notes" rows="2" placeholder="Quality check inspection remarks..."
                                  class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="openCreditModal = false" class="px-4 py-2 rounded-xl text-slate-600 bg-slate-100 hover:bg-slate-200 font-bold">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black shadow-md shadow-emerald-600/20">
                            Post Credit Note
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layouts.app>
