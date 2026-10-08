<x-layouts.app>
    <x-slot:title>Debit Notes</x-slot:title>

    <div class="space-y-6" x-data="{ openDebitModal: false }">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Supplier Debit Notes</h1>
                <p class="text-xs text-slate-500 mt-0.5">Claims against suppliers for defective preforms, packaging rejects, and billing disputes.</p>
            </div>
            
            <button type="button" @click="openDebitModal = true"
                    class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Issue Debit Note</span>
            </button>
        </div>

        <!-- Debit Notes Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4 font-bold">DEBIT NOTE NO</th>
                            <th class="py-3 px-4 font-bold">DATE</th>
                            <th class="py-3 px-4 font-bold">VENDOR</th>
                            <th class="py-3 px-4 font-bold">SUPPLIER BILL REF</th>
                            <th class="py-3 px-4 font-bold">REASON FOR REJECTION</th>
                            <th class="py-3 px-4 font-bold text-right">DEBIT AMOUNT (KES)</th>
                            <th class="py-3 px-4 font-bold text-center">STATUS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($debitNotes as $dn)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-emerald-800">{{ $dn->debit_note_no }}</td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $dn->debit_date->format('d M Y') }}</td>
                                <td class="py-3.5 px-4 font-semibold text-slate-900">{{ $dn->vendor->name ?? 'N/A' }}</td>
                                <td class="py-3.5 px-4 font-mono text-slate-600">{{ $dn->invoice->invoice_no ?? 'Direct Account Adjustment' }}</td>
                                <td class="py-3.5 px-4 text-slate-700">
                                    <div class="font-medium text-slate-900">{{ $dn->reason }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $dn->notes }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-right font-black text-rose-600">
                                    KES {{ number_format($dn->amount, 2) }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        {{ $dn->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400">No debit notes recorded.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($debitNotes->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $debitNotes->links() }}
                </div>
            @endif
        </div>

        <!-- MODAL: ISSUE DEBIT NOTE -->
        <div x-cloak x-show="openDebitModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 border border-slate-100" @click.away="openDebitModal = false">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-black text-slate-900">Issue Debit Note to Supplier</h3>
                    <button type="button" @click="openDebitModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                </div>

                <form method="POST" action="{{ route('payables.debit-notes.store') }}" class="space-y-3 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Vendor / Supplier</label>
                        <select name="vendor_id" required class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                            @foreach ($vendors as $v)
                                <option value="{{ $v->id }}">{{ $v->name }} (Balance Owed: KES {{ number_format($v->current_balance) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Invoice Reference (Optional)</label>
                            <select name="purchase_invoice_id" class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                                <option value="">Direct Creditor Adjustment</option>
                                @foreach ($invoices as $inv)
                                    <option value="{{ $inv->id }}">{{ $inv->invoice_no }} ({{ $inv->vendor->name ?? '' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Debit Date</label>
                            <input type="date" name="debit_date" required value="{{ date('Y-m-d') }}"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Debit Amount (KES)</label>
                        <input type="number" step="0.01" name="amount" required placeholder="e.g. 5000"
                               class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Reason for Debit</label>
                        <select name="reason" class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                            <option value="Defective / Deformed PET Preforms Rejected">Defective / Deformed PET Preforms Rejected</option>
                            <option value="Damaged Tamper-Evident Caps">Damaged Tamper-Evident Caps</option>
                            <option value="Shortage in Delivered Delivery Note">Shortage in Delivered Delivery Note</option>
                            <option value="Invoice Price Discrepancy Correction">Invoice Price Discrepancy Correction</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Quality Inspection Details</label>
                        <textarea name="notes" rows="2" placeholder="Inspection report and return verification..."
                                  class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="openDebitModal = false" class="px-4 py-2 rounded-xl text-slate-600 bg-slate-100 hover:bg-slate-200 font-bold">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black shadow-md shadow-emerald-600/20">
                            Post Debit Note
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layouts.app>
