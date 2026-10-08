<x-layouts.app>
    <x-slot:title>Customer Directory & Balances</x-slot:title>

    <div class="space-y-6" x-data="{ openCustomerModal: false }">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Customer Directory & Balances</h1>
                <p class="text-xs text-slate-500 mt-0.5">Wholesale supermarket distributors, corporate dispenser clients, and retail outlets.</p>
            </div>
            
            <div class="flex items-center gap-2">
                <a href="{{ route('sales.aging-report') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 shadow-xs">
                    AR Aging Report &rarr;
                </a>
                <button type="button" @click="openCustomerModal = true"
                        class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Add New Customer</span>
                </button>
            </div>
        </div>

        <!-- Summary Banner -->
        <div class="bg-gradient-to-r from-emerald-900 to-slate-900 text-white p-5 rounded-2xl shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider">TOTAL OUTSTANDING ACCOUNTS RECEIVABLE (AR)</span>
                <div class="text-3xl font-black mt-1">KES {{ number_format($totalReceivables, 2) }}</div>
                <p class="text-xs text-emerald-300/80 mt-0.5">Trade debtor balances across {{ $customers->count() }} active accounts</p>
            </div>
            <a href="{{ route('sales.receipts') }}" class="px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs shadow-md">
                Record Customer Receipt
            </a>
        </div>

        <!-- Customer List Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4 font-bold">CODE</th>
                            <th class="py-3 px-4 font-bold">CUSTOMER NAME</th>
                            <th class="py-3 px-4 font-bold">TYPE</th>
                            <th class="py-3 px-4 font-bold">CONTACT / PHONE</th>
                            <th class="py-3 px-4 font-bold">TAX PIN</th>
                            <th class="py-3 px-4 font-bold text-right">CREDIT LIMIT</th>
                            <th class="py-3 px-4 font-bold text-right">PAYMENT TERMS</th>
                            <th class="py-3 px-4 font-bold text-right">CURRENT BALANCE</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($customers as $c)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-emerald-800">{{ $c->code }}</td>
                                <td class="py-3.5 px-4 font-semibold text-slate-900">
                                    {{ $c->name }}
                                    <div class="text-[10px] text-slate-400">{{ $c->address }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-700">
                                        {{ $c->customer_type }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    <div>{{ $c->phone ?? 'N/A' }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $c->email }}</div>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-slate-500">{{ $c->tax_pin ?? 'N/A' }}</td>
                                <td class="py-3.5 px-4 text-right text-slate-600">KES {{ number_format($c->credit_limit, 2) }}</td>
                                <td class="py-3.5 px-4 text-right text-slate-600">{{ $c->payment_terms_days }} Days</td>
                                <td class="py-3.5 px-4 text-right font-black {{ $c->current_balance > 0 ? 'text-rose-600' : 'text-slate-900' }}">
                                    KES {{ number_format($c->current_balance, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-slate-400">No customers registered yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL: ADD CUSTOMER -->
        <div x-cloak x-show="openCustomerModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 border border-slate-100" @click.away="openCustomerModal = false">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-black text-slate-900">Add New Customer Account</h3>
                    <button type="button" @click="openCustomerModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                </div>

                <form method="POST" action="{{ route('sales.customers.store') }}" class="space-y-3 text-xs">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Customer Code</label>
                            <input type="text" name="code" required placeholder="e.g. CUST-005"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Customer Type</label>
                            <select name="customer_type" class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                                <option value="Wholesale">Wholesale Distributor</option>
                                <option value="Retail/Supermarket">Retail / Supermarket</option>
                                <option value="Corporate">Corporate Office</option>
                                <option value="Walk-In/Refill">Walk-In / Refill</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Customer Full Name / Company</label>
                        <input type="text" name="name" required placeholder="e.g. Carrefour Supermarket Sarit Centre"
                               class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Phone Number</label>
                            <input type="text" name="phone" placeholder="+254 7..."
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Email</label>
                            <input type="email" name="email" placeholder="orders@customer.co.ke"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Physical Delivery Address</label>
                            <input type="text" name="address" placeholder="Westlands, Nairobi"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">KRA Tax PIN</label>
                            <input type="text" name="tax_pin" placeholder="P051..."
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Credit Limit (KES)</label>
                            <input type="number" step="0.01" name="credit_limit" value="100000" required
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Payment Terms (Days)</label>
                            <input type="number" name="payment_terms_days" value="30" required
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Opening Balance</label>
                            <input type="number" step="0.01" name="opening_balance" value="0" required
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="openCustomerModal = false" class="px-4 py-2 rounded-xl text-slate-600 bg-slate-100 hover:bg-slate-200 font-bold">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black shadow-md shadow-emerald-600/20">
                            Save Customer
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layouts.app>
