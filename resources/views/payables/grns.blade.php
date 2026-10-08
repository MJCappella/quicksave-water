<x-layouts.app>
    <x-slot:title>Goods Received Notes (G.R.N)</x-slot:title>

    <div class="space-y-6" x-data="{ openGrnModal: false }">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Goods Received Notes (G.R.N)</h1>
                <p class="text-xs text-slate-500 mt-0.5">Physical receipt, QA verification, and store acceptance against Purchase Orders.</p>
            </div>
            
            <button type="button" @click="openGrnModal = true"
                    class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Record New G.R.N</span>
            </button>
        </div>

        <!-- GRN Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4 font-bold">GRN NO</th>
                            <th class="py-3 px-4 font-bold">DATE</th>
                            <th class="py-3 px-4 font-bold">LPO REF</th>
                            <th class="py-3 px-4 font-bold">VENDOR</th>
                            <th class="py-3 px-4 font-bold">RECEIVING STORE</th>
                            <th class="py-3 px-4 font-bold">DELIVERY NOTE REF</th>
                            <th class="py-3 px-4 font-bold">RECEIVED BY</th>
                            <th class="py-3 px-4 font-bold text-center">STATUS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($grns as $grn)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-emerald-800">{{ $grn->grn_no }}</td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $grn->received_date->format('d M Y') }}</td>
                                <td class="py-3.5 px-4 font-mono text-slate-600">{{ $grn->purchaseOrder->po_no ?? 'Direct Purchase' }}</td>
                                <td class="py-3.5 px-4 font-semibold text-slate-900">{{ $grn->vendor->name ?? 'N/A' }}</td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $grn->store->name ?? 'Main Plant' }}</td>
                                <td class="py-3.5 px-4 font-mono text-slate-600">{{ $grn->delivery_note_ref ?? 'N/A' }}</td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $grn->received_by }}</td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        {{ $grn->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-slate-400">No goods received notes logged.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($grns->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $grns->links() }}
                </div>
            @endif
        </div>

        <!-- MODAL: RECORD GRN -->
        <div x-cloak x-show="openGrnModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 border border-slate-100" @click.away="openGrnModal = false">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-black text-slate-900">Receive Inbound Goods (G.R.N)</h3>
                    <button type="button" @click="openGrnModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                </div>

                <form method="POST" action="{{ route('payables.grns.store') }}" class="space-y-3 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Vendor / Supplier</label>
                        <select name="vendor_id" required class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                            @foreach ($vendors as $v)
                                <option value="{{ $v->id }}">{{ $v->name }} ({{ $v->category }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Purchase Order (Optional)</label>
                            <select name="purchase_order_id" class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                                <option value="">Direct Store Receiving</option>
                                @foreach ($purchaseOrders as $po)
                                    <option value="{{ $po->id }}">{{ $po->po_no }} - {{ $po->vendor->name ?? '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Receiving Warehouse</label>
                            <select name="store_id" required class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                                @foreach ($stores as $st)
                                    <option value="{{ $st->id }}">{{ $st->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Received Date</label>
                            <input type="date" name="received_date" required value="{{ date('Y-m-d') }}"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Supplier Delivery Note #</label>
                            <input type="text" name="delivery_note_ref" placeholder="e.g. DN-PLP-8921"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                    </div>

                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 space-y-2">
                        <span class="block font-bold text-slate-800">Received Item Inspection</span>
                        <div>
                            <label class="block text-[10px] text-slate-500 mb-1">Item Description</label>
                            <input type="text" name="items[0][item_description]" required placeholder="e.g. 500ml PET Preforms"
                                   class="w-full rounded-lg border-slate-300 py-1.5 px-2 text-xs">
                        </div>
                        <div class="grid grid-cols-4 gap-2">
                            <div>
                                <label class="block text-[10px] text-slate-500 mb-1">Ordered</label>
                                <input type="number" step="0.01" name="items[0][quantity_ordered]" value="20000" required
                                       class="w-full rounded-lg border-slate-300 py-1.5 px-2 text-xs">
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-500 mb-1">Delivered</label>
                                <input type="number" step="0.01" name="items[0][quantity_received]" value="20000" required
                                       class="w-full rounded-lg border-slate-300 py-1.5 px-2 text-xs">
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-500 mb-1">Accepted</label>
                                <input type="number" step="0.01" name="items[0][quantity_accepted]" value="20000" required
                                       class="w-full rounded-lg border-slate-300 py-1.5 px-2 text-xs">
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-500 mb-1">Unit Cost</label>
                                <input type="number" step="0.01" name="items[0][unit_cost]" value="3.80" required
                                       class="w-full rounded-lg border-slate-300 py-1.5 px-2 text-xs">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Storekeeper / Received By</label>
                            <input type="text" name="received_by" value="Storekeeper Peter Karanja" required
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Inspection Notes</label>
                            <input type="text" name="notes" placeholder="Condition verified clean & undamaged"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="openGrnModal = false" class="px-4 py-2 rounded-xl text-slate-600 bg-slate-100 hover:bg-slate-200 font-bold">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black shadow-md shadow-emerald-600/20">
                            Verify & Accept G.R.N
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layouts.app>
