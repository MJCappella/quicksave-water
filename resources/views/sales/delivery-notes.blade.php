<x-layouts.app>
    <x-slot:title>Delivery Notes</x-slot:title>

    <div class="space-y-6" x-data="{ openDeliveryModal: false }">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Dispatch Delivery Notes</h1>
                <p class="text-xs text-slate-500 mt-0.5">Proof of dispatch and customer gate pass for delivery trucks and vans.</p>
            </div>
            
            <button type="button" @click="openDeliveryModal = true"
                    class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Dispatch New Delivery Note</span>
            </button>
        </div>

        <!-- Delivery Notes Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4 font-bold">DELIVERY NO</th>
                            <th class="py-3 px-4 font-bold">DELIVERY DATE</th>
                            <th class="py-3 px-4 font-bold">CUSTOMER</th>
                            <th class="py-3 px-4 font-bold">DISPATCH LOCATION</th>
                            <th class="py-3 px-4 font-bold">DRIVER & TRUCK</th>
                            <th class="py-3 px-4 font-bold">DELIVERED QUANTITY</th>
                            <th class="py-3 px-4 font-bold text-center">STATUS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($deliveryNotes as $dn)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-emerald-800">{{ $dn->delivery_no }}</td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $dn->delivery_date->format('d M Y') }}</td>
                                <td class="py-3.5 px-4 font-semibold text-slate-900">{{ $dn->customer->name ?? 'N/A' }}</td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $dn->store->name ?? 'Superior Center' }}</td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    <div>{{ $dn->driver_name ?? 'Samuel Gitonga' }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">{{ $dn->vehicle_reg ?? 'KBZ 740W' }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-slate-700">
                                    <ul class="list-disc list-inside space-y-0.5 text-[11px]">
                                        @foreach ($dn->items as $item)
                                            <li>{{ $item->product->name ?? 'Water' }}: <span class="font-bold text-slate-900">{{ $item->quantity_delivered }} units</span></li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        {{ $dn->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400">No delivery notes issued yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($deliveryNotes->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $deliveryNotes->links() }}
                </div>
            @endif
        </div>

        <!-- MODAL: CREATE DELIVERY NOTE -->
        <div x-cloak x-show="openDeliveryModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 border border-slate-100" @click.away="openDeliveryModal = false">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-black text-slate-900">Dispatch Delivery Note</h3>
                    <button type="button" @click="openDeliveryModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                </div>

                <form method="POST" action="{{ route('sales.delivery-notes.store') }}" class="space-y-3 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Customer</label>
                        <select name="customer_id" required class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                            @foreach ($customers as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->address }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Dispatching Store</label>
                            <select name="store_id" required class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                                @foreach ($stores as $st)
                                    <option value="{{ $st->id }}">{{ $st->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Delivery Date</label>
                            <input type="date" name="delivery_date" required value="{{ date('Y-m-d') }}"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Driver Name</label>
                            <input type="text" name="driver_name" value="Samuel Gitonga" required
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Vehicle Registration</label>
                            <input type="text" name="vehicle_reg" value="KBZ 740W" required
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                    </div>

                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 space-y-2">
                        <span class="block font-bold text-slate-800">Dispatch Item</span>
                        <div>
                            <label class="block text-[10px] text-slate-500 mb-1">Product</label>
                            <select name="items[0][product_id]" class="w-full rounded-lg border-slate-300 py-1.5 px-2 text-xs">
                                @foreach ($products as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[10px] text-slate-500 mb-1">Qty Ordered</label>
                                <input type="number" name="items[0][quantity_ordered]" value="50" required min="1"
                                       class="w-full rounded-lg border-slate-300 py-1.5 px-2 text-xs">
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-500 mb-1">Qty Delivered</label>
                                <input type="number" name="items[0][quantity_delivered]" value="50" required min="1"
                                       class="w-full rounded-lg border-slate-300 py-1.5 px-2 text-xs">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Delivery Notes & Receiver Details</label>
                        <textarea name="notes" rows="2" placeholder="Delivered in good order and condition..."
                                  class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="openDeliveryModal = false" class="px-4 py-2 rounded-xl text-slate-600 bg-slate-100 hover:bg-slate-200 font-bold">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black shadow-md shadow-emerald-600/20">
                            Issue Delivery Note
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layouts.app>
