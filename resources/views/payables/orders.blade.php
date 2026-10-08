<x-layouts.app>
    <x-slot:title>Purchase Orders (LPO)</x-slot:title>

    <div class="space-y-6" x-data="{ openPoModal: false }">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Official Purchase Orders (LPO)</h1>
                <p class="text-xs text-slate-500 mt-0.5">Legally binding Local Purchase Orders issued to raw materials and packaging vendors.</p>
            </div>
            
            <button type="button" @click="openPoModal = true"
                    class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Create Purchase Order (LPO)</span>
            </button>
        </div>

        <!-- Orders Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4 font-bold">LPO NO</th>
                            <th class="py-3 px-4 font-bold">ORDER DATE</th>
                            <th class="py-3 px-4 font-bold">VENDOR / SUPPLIER</th>
                            <th class="py-3 px-4 font-bold">EXPECTED DELIVERY</th>
                            <th class="py-3 px-4 font-bold text-right">SUBTOTAL</th>
                            <th class="py-3 px-4 font-bold text-right">TOTAL (KES)</th>
                            <th class="py-3 px-4 font-bold text-center">STATUS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($orders as $po)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-emerald-800">{{ $po->po_no }}</td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $po->order_date->format('d M Y') }}</td>
                                <td class="py-3.5 px-4 font-semibold text-slate-900">{{ $po->vendor->name ?? 'N/A' }}</td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $po->expected_delivery_date ? $po->expected_delivery_date->format('d M Y') : 'N/A' }}</td>
                                <td class="py-3.5 px-4 text-right text-slate-600">KES {{ number_format($po->subtotal, 2) }}</td>
                                <td class="py-3.5 px-4 text-right font-black text-slate-900">KES {{ number_format($po->grand_total, 2) }}</td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        {{ $po->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400">No purchase orders generated.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($orders->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>

        <!-- MODAL: CREATE PO -->
        <div x-cloak x-show="openPoModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 border border-slate-100" @click.away="openPoModal = false">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-black text-slate-900">Generate Purchase Order (LPO)</h3>
                    <button type="button" @click="openPoModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                </div>

                <form method="POST" action="{{ route('payables.orders.store') }}" class="space-y-3 text-xs">
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
                            <label class="block font-bold text-slate-700 mb-1">Order Date</label>
                            <input type="date" name="order_date" required value="{{ date('Y-m-d') }}"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Delivery Due Date</label>
                            <input type="date" name="expected_delivery_date" value="{{ date('Y-m-d', strtotime('+5 days')) }}"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                    </div>

                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 space-y-2">
                        <span class="block font-bold text-slate-800">Ordered Item Line</span>
                        <div>
                            <label class="block text-[10px] text-slate-500 mb-1">Item Description</label>
                            <input type="text" name="items[0][item_description]" required placeholder="e.g. PET Preforms 500ml 18g Clear (Virgin Resin)"
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
                        <label class="block font-bold text-slate-700 mb-1">Special Instructions</label>
                        <textarea name="notes" rows="2" placeholder="Inspection upon arrival at Superior Center Plant..."
                                  class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="openPoModal = false" class="px-4 py-2 rounded-xl text-slate-600 bg-slate-100 hover:bg-slate-200 font-bold">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black shadow-md shadow-emerald-600/20">
                            Issue Official LPO
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layouts.app>
