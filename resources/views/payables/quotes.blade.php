<x-layouts.app>
    <x-slot:title>Supplier Purchase Quotes</x-slot:title>

    <div class="space-y-6" x-data="{ openQuoteModal: false }">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Supplier Purchase Quotations</h1>
                <p class="text-xs text-slate-500 mt-0.5">Price comparisons and formal vendor proformas for material procurement.</p>
            </div>
            
            <button type="button" @click="openQuoteModal = true"
                    class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Record Vendor Quote</span>
            </button>
        </div>

        <!-- Quotes Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4 font-bold">QUOTE REF</th>
                            <th class="py-3 px-4 font-bold">VENDOR</th>
                            <th class="py-3 px-4 font-bold">PR LINK</th>
                            <th class="py-3 px-4 font-bold">DATE</th>
                            <th class="py-3 px-4 font-bold">VALID UNTIL</th>
                            <th class="py-3 px-4 font-bold text-right">TOTAL QUOTED (KES)</th>
                            <th class="py-3 px-4 font-bold text-center">STATUS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($quotes as $pq)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-emerald-800">{{ $pq->quote_no }}</td>
                                <td class="py-3.5 px-4 font-semibold text-slate-900">{{ $pq->vendor->name ?? 'N/A' }}</td>
                                <td class="py-3.5 px-4 font-mono text-slate-600">{{ $pq->requisition->requisition_no ?? 'N/A' }}</td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $pq->quote_date->format('d M Y') }}</td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $pq->valid_until->format('d M Y') }}</td>
                                <td class="py-3.5 px-4 text-right font-black text-slate-900">KES {{ number_format($pq->total_amount, 2) }}</td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        {{ $pq->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400">No vendor quotations recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($quotes->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $quotes->links() }}
                </div>
            @endif
        </div>

        <!-- MODAL: RECORD VENDOR QUOTE -->
        <div x-cloak x-show="openQuoteModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 border border-slate-100" @click.away="openQuoteModal = false">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-black text-slate-900">Record Vendor Price Quote</h3>
                    <button type="button" @click="openQuoteModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                </div>

                <form method="POST" action="{{ route('payables.quotes.store') }}" class="space-y-3 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Vendor / Supplier</label>
                        <select name="vendor_id" required class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                            @foreach ($vendors as $v)
                                <option value="{{ $v->id }}">{{ $v->name }} ({{ $v->category }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Linked Purchase Requisition (Optional)</label>
                        <select name="purchase_requisition_id" class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                            <option value="">Direct Procurement Quote</option>
                            @foreach ($requisitions as $pr)
                                <option value="{{ $pr->id }}">{{ $pr->requisition_no }} - {{ $pr->department }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Quote Date</label>
                            <input type="date" name="quote_date" required value="{{ date('Y-m-d') }}"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Valid Until</label>
                            <input type="date" name="valid_until" required value="{{ date('Y-m-d', strtotime('+30 days')) }}"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Total Quoted Amount (KES)</label>
                        <input type="number" step="0.01" name="total_amount" required placeholder="e.g. 114000"
                               class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Quotation Notes / Terms</label>
                        <textarea name="notes" rows="2" placeholder="Delivery time, payment credit terms..."
                                  class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="openQuoteModal = false" class="px-4 py-2 rounded-xl text-slate-600 bg-slate-100 hover:bg-slate-200 font-bold">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black shadow-md shadow-emerald-600/20">
                            Save Supplier Quote
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layouts.app>
