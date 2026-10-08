<x-layouts.app>
    <x-slot:title>Purchase Requisitions</x-slot:title>

    <div class="space-y-6" x-data="{ openReqModal: false }">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Purchase Requisitions (PR)</h1>
                <p class="text-xs text-slate-500 mt-0.5">Internal departmental procurement requests for factory materials, spares, and utilities.</p>
            </div>
            
            <button type="button" @click="openReqModal = true"
                    class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>New Purchase Requisition</span>
            </button>
        </div>

        <!-- Requisitions Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4 font-bold">REQUISITION NO</th>
                            <th class="py-3 px-4 font-bold">DATE</th>
                            <th class="py-3 px-4 font-bold">DEPARTMENT</th>
                            <th class="py-3 px-4 font-bold">REQUESTED BY</th>
                            <th class="py-3 px-4 font-bold">PURPOSE</th>
                            <th class="py-3 px-4 font-bold text-right">ESTIMATED TOTAL</th>
                            <th class="py-3 px-4 font-bold text-center">PRIORITY</th>
                            <th class="py-3 px-4 font-bold text-center">STATUS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($requisitions as $pr)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-emerald-800">{{ $pr->requisition_no }}</td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $pr->requisition_date->format('d M Y') }}</td>
                                <td class="py-3.5 px-4 font-semibold text-slate-900">{{ $pr->department }}</td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $pr->requested_by }}</td>
                                <td class="py-3.5 px-4 text-slate-700 max-w-xs truncate">{{ $pr->purpose }}</td>
                                <td class="py-3.5 px-4 text-right font-black text-slate-900">KES {{ number_format($pr->estimated_total, 2) }}</td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ $pr->priority === 'High' ? 'bg-rose-100 text-rose-800' : 'bg-slate-100 text-slate-700' }}">
                                        {{ $pr->priority }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        {{ $pr->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-slate-400">No purchase requisitions lodged yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($requisitions->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $requisitions->links() }}
                </div>
            @endif
        </div>

        <!-- MODAL: LODGE REQUISITION -->
        <div x-cloak x-show="openReqModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 border border-slate-100" @click.away="openReqModal = false">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-black text-slate-900">Lodge Purchase Requisition</h3>
                    <button type="button" @click="openReqModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                </div>

                <form method="POST" action="{{ route('payables.requisitions.store') }}" class="space-y-3 text-xs">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Requesting Department</label>
                            <select name="department" class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                                <option value="Production & Bottling">Production & Bottling</option>
                                <option value="Quality Control & Lab">Quality Control & Lab</option>
                                <option value="Maintenance & Engineering">Maintenance & Engineering</option>
                                <option value="Transport & Fleet">Transport & Fleet</option>
                                <option value="Administration & Operations">Administration & Operations</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Requested By</label>
                            <input type="text" name="requested_by" value="Dennis Kipchumba" required
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Date</label>
                            <input type="date" name="requisition_date" required value="{{ date('Y-m-d') }}"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Required By Date</label>
                            <input type="date" name="required_date" required value="{{ date('Y-m-d', strtotime('+7 days')) }}"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Priority</label>
                            <select name="priority" class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                                <option value="Urgent">Urgent</option>
                            </select>
                        </div>
                    </div>

                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 space-y-2">
                        <span class="block font-bold text-slate-800">Requested Item Description</span>
                        <div>
                            <label class="block text-[10px] text-slate-500 mb-1">Item Details</label>
                            <input type="text" name="items[0][item_description]" required placeholder="e.g. 20-Inch Active Carbon Cartridges"
                                   class="w-full rounded-lg border-slate-300 py-1.5 px-2 text-xs">
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="block text-[10px] text-slate-500 mb-1">Qty</label>
                                <input type="number" step="0.01" name="items[0][quantity]" value="10" required
                                       class="w-full rounded-lg border-slate-300 py-1.5 px-2 text-xs">
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-500 mb-1">Unit</label>
                                <input type="text" name="items[0][unit]" value="pcs" required
                                       class="w-full rounded-lg border-slate-300 py-1.5 px-2 text-xs">
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-500 mb-1">Est. Unit Cost (KES)</label>
                                <input type="number" step="0.01" name="items[0][estimated_unit_cost]" value="1850" required
                                       class="w-full rounded-lg border-slate-300 py-1.5 px-2 text-xs">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Operational Purpose / Justification</label>
                        <textarea name="purpose" rows="2" required placeholder="Pre-treatment filtration media replacement for RO line..."
                                  class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="openReqModal = false" class="px-4 py-2 rounded-xl text-slate-600 bg-slate-100 hover:bg-slate-200 font-bold">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black shadow-md shadow-emerald-600/20">
                            Submit Requisition
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layouts.app>
