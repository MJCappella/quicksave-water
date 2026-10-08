<x-layouts.app>
    <x-slot:title>Inter-Store Transfers</x-slot:title>

    <div class="space-y-6" x-data="{ openTransferModal: false }">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Inter-Store Transfers</h1>
                <p class="text-xs text-slate-500 mt-0.5">Dispatches between Factory Warehouse, Regional Depots, and Mobile Van Trucks.</p>
            </div>
            
            <div class="flex items-center gap-2">
                <button type="button" @click="openTransferModal = true"
                        class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Create Inter-Store Transfer</span>
                </button>
            </div>
        </div>

        <!-- Transfers Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Transfer Dispatches History</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Double-entry stock movements automatically recorded for both locations.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4 font-bold">TRANSFER NO</th>
                            <th class="py-3 px-4 font-bold">DATE</th>
                            <th class="py-3 px-4 font-bold">SOURCE LOCATION</th>
                            <th class="py-3 px-4 font-bold">DESTINATION LOCATION</th>
                            <th class="py-3 px-4 font-bold">DRIVER & VEHICLE</th>
                            <th class="py-3 px-4 font-bold">TRANSFERRED ITEMS</th>
                            <th class="py-3 px-4 font-bold text-center">STATUS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($transfers as $trf)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-emerald-800">{{ $trf->transfer_no }}</td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $trf->transfer_date->format('d M Y') }}</td>
                                <td class="py-3.5 px-4 font-semibold text-slate-900">{{ $trf->fromStore->name ?? 'N/A' }}</td>
                                <td class="py-3.5 px-4 font-semibold text-slate-900">{{ $trf->toStore->name ?? 'N/A' }}</td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    <div>{{ $trf->driver_name ?? 'Fleet Driver' }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">{{ $trf->vehicle_reg ?? 'KBZ 740W' }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-slate-700">
                                    <ul class="list-disc list-inside space-y-0.5 text-[11px]">
                                        @foreach ($trf->items as $item)
                                            <li>{{ $item->product->name ?? 'Water' }}: <span class="font-bold">{{ $item->quantity_sent }}</span></li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        {{ $trf->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-6 text-center text-slate-400">No store transfers logged.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($transfers->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $transfers->links() }}
                </div>
            @endif
        </div>

        <!-- MODAL: CREATE STORE TRANSFER -->
        <div x-cloak x-show="openTransferModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 border border-slate-100" @click.away="openTransferModal = false">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-black text-slate-900">Initiate Inter-Store Transfer</h3>
                    <button type="button" @click="openTransferModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                </div>

                <form method="POST" action="{{ route('inventory.transfers.store') }}" class="space-y-3 text-xs">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">From Store (Source)</label>
                            <select name="from_store_id" required class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                                @foreach ($stores as $st)
                                    <option value="{{ $st->id }}">{{ $st->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">To Store (Destination)</label>
                            <select name="to_store_id" required class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                                @foreach ($stores as $st)
                                    <option value="{{ $st->id }}" {{ $loop->iteration == 2 ? 'selected' : '' }}>{{ $st->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Date</label>
                            <input type="date" name="transfer_date" required value="{{ date('Y-m-d') }}"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Driver</label>
                            <input type="text" name="driver_name" placeholder="Peter Karanja"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Vehicle Reg</label>
                            <input type="text" name="vehicle_reg" placeholder="KDA 421B"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                    </div>

                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 space-y-2">
                        <span class="block font-bold text-slate-800">Item to Dispatch</span>
                        <div class="grid grid-cols-3 gap-2">
                            <div class="col-span-2">
                                <label class="block text-[10px] text-slate-500 mb-1">Product</label>
                                <select name="items[0][product_id]" class="w-full rounded-lg border-slate-300 py-1.5 px-2 text-xs">
                                    @foreach ($products as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-500 mb-1">Quantity</label>
                                <input type="number" name="items[0][quantity]" required min="1" value="50"
                                       class="w-full rounded-lg border-slate-300 py-1.5 px-2 text-xs">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Dispatch Notes</label>
                        <textarea name="notes" rows="2" placeholder="Morning delivery route dispatch..."
                                  class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="openTransferModal = false" class="px-4 py-2 rounded-xl text-slate-600 bg-slate-100 hover:bg-slate-200 font-bold">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black shadow-md shadow-emerald-600/20">
                            Execute Transfer
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layouts.app>
