<x-layouts.app>
    <x-slot:title>Vendor List & Balances Report</x-slot:title>

    <div class="space-y-6" x-data="{ openVendorModal: false }">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Vendor Creditors & Balances</h1>
                <p class="text-xs text-slate-500 mt-0.5">Preforms manufacturers, label printers, water treatment chemicals, and plant engineering suppliers.</p>
            </div>
            
            <div class="flex items-center gap-2">
                <a href="{{ route('payables.aging-report') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 shadow-xs">
                    AP Aging Report &rarr;
                </a>
                <button type="button" @click="openVendorModal = true"
                        class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Register New Vendor</span>
                </button>
            </div>
        </div>

        <!-- Summary Banner -->
        <div class="bg-gradient-to-r from-slate-900 to-emerald-950 text-white p-5 rounded-2xl shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider">TOTAL ACCOUNTS PAYABLE (TRADE CREDITORS)</span>
                <div class="text-3xl font-black mt-1">KES {{ number_format($totalPayables, 2) }}</div>
                <p class="text-xs text-emerald-300/80 mt-0.5">Total supplier balances owed across {{ $vendors->count() }} active vendors</p>
            </div>
            <a href="{{ route('payables.vouchers') }}" class="px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs shadow-md">
                Authorize Payment Voucher
            </a>
        </div>

        <!-- Vendors Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4 font-bold">VENDOR CODE</th>
                            <th class="py-3 px-4 font-bold">SUPPLIER NAME</th>
                            <th class="py-3 px-4 font-bold">CATEGORY</th>
                            <th class="py-3 px-4 font-bold">PHONE / EMAIL</th>
                            <th class="py-3 px-4 font-bold">KRA PIN</th>
                            <th class="py-3 px-4 font-bold text-right">CREDIT TERMS</th>
                            <th class="py-3 px-4 font-bold text-right">CURRENT BALANCE DUE</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($vendors as $v)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-emerald-800">{{ $v->code }}</td>
                                <td class="py-3.5 px-4 font-semibold text-slate-900">
                                    {{ $v->name }}
                                    <div class="text-[10px] text-slate-400">{{ $v->address }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-700">
                                        {{ $v->category }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    <div>{{ $v->phone ?? 'N/A' }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $v->email }}</div>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-slate-500">{{ $v->tax_pin ?? 'N/A' }}</td>
                                <td class="py-3.5 px-4 text-right text-slate-600">{{ $v->payment_terms_days }} Days</td>
                                <td class="py-3.5 px-4 text-right font-black {{ $v->current_balance > 0 ? 'text-rose-600' : 'text-slate-900' }}">
                                    KES {{ number_format($v->current_balance, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400">No vendors registered yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL: REGISTER VENDOR -->
        <div x-cloak x-show="openVendorModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 border border-slate-100" @click.away="openVendorModal = false">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-black text-slate-900">Register Supplier / Vendor</h3>
                    <button type="button" @click="openVendorModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                </div>

                <form method="POST" action="{{ route('payables.vendors.store') }}" class="space-y-3 text-xs">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Vendor Code</label>
                            <input type="text" name="code" required placeholder="e.g. VND-004"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Category</label>
                            <select name="category" class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                                <option value="Raw Materials">Raw Materials (Preforms/Caps)</option>
                                <option value="Packaging & Labels">Packaging & Labels</option>
                                <option value="Machinery & Spares">Machinery & Spares</option>
                                <option value="Fuel & Logistics">Fuel & Logistics</option>
                                <option value="Factory Utilities">Factory Utilities</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Vendor Company Name</label>
                        <input type="text" name="name" required placeholder="e.g. Kenya Plastics & Preforms Ltd"
                               class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Phone</label>
                            <input type="text" name="phone" placeholder="+254 7..."
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Email</label>
                            <input type="email" name="email" placeholder="sales@supplier.co.ke"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Address / Location</label>
                            <input type="text" name="address" placeholder="Industrial Area, Nairobi"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">KRA Tax PIN</label>
                            <input type="text" name="tax_pin" placeholder="P051..."
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Payment Terms (Days)</label>
                            <input type="number" name="payment_terms_days" value="30" required
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Opening Balance (KES)</label>
                            <input type="number" step="0.01" name="opening_balance" value="0" required
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="openVendorModal = false" class="px-4 py-2 rounded-xl text-slate-600 bg-slate-100 hover:bg-slate-200 font-bold">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black shadow-md shadow-emerald-600/20">
                            Save Vendor
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layouts.app>
