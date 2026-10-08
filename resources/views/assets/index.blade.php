<x-layouts.app>
    <x-slot:title>Fixed Asset Register</x-slot:title>

    <div class="space-y-6" x-data="{ openAssetModal: false }">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Fixed Asset Register & Depreciation</h1>
                <p class="text-xs text-slate-500 mt-0.5">RO water purification plant, automated bottling machinery, delivery fleet, and client field dispensers.</p>
            </div>
            
            <button type="button" @click="openAssetModal = true"
                    class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Register New Fixed Asset</span>
            </button>
        </div>

        <!-- Summary KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">TOTAL HISTORICAL COST</span>
                <div class="text-2xl font-black text-slate-900 mt-1">
                    KES {{ number_format($totalOriginalCost, 2) }}
                </div>
                <p class="text-[11px] text-slate-500 font-medium mt-1">Total capital acquisition cost</p>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">ACCUMULATED DEPRECIATION</span>
                <div class="text-2xl font-black text-rose-600 mt-1">
                    KES {{ number_format($totalAccumulatedDepr, 2) }}
                </div>
                <p class="text-[11px] text-slate-500 font-medium mt-1">Wear & tear wear-off to date</p>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">NET BOOK VALUE (NBV)</span>
                <div class="text-2xl font-black text-emerald-700 mt-1">
                    KES {{ number_format($totalNetBookValue, 2) }}
                </div>
                <p class="text-[11px] text-emerald-600 font-medium mt-1">Current balance sheet valuation</p>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">ACTIVE ASSETS</span>
                <div class="text-2xl font-black text-slate-900 mt-1">
                    {{ $activeAssetsCount }} / {{ $assets->count() }}
                </div>
                <p class="text-[11px] text-slate-500 font-medium mt-1">In operational service</p>
            </div>
        </div>

        <!-- Filter bar -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between gap-4">
            <form method="GET" action="{{ route('assets.index') }}" class="flex items-center gap-2">
                <label for="filter-cat" class="text-xs font-bold text-slate-600">Category Filter:</label>
                <select id="filter-cat" name="category" onchange="this.form.submit()"
                        class="text-xs rounded-xl border-slate-300 bg-white py-1.5 px-3 text-slate-800 font-semibold focus:border-emerald-500">
                    <option value="">All Categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat }}" {{ $selectedCategory == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <!-- Fixed Asset Register Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4 font-bold">ASSET CODE</th>
                            <th class="py-3 px-4 font-bold">ASSET NAME / MODEL</th>
                            <th class="py-3 px-4 font-bold">CATEGORY</th>
                            <th class="py-3 px-4 font-bold">PURCHASE DATE</th>
                            <th class="py-3 px-4 font-bold text-right">PURCHASE COST</th>
                            <th class="py-3 px-4 font-bold text-right">ACCUM. DEPR.</th>
                            <th class="py-3 px-4 font-bold text-right">NET BOOK VALUE</th>
                            <th class="py-3 px-4 font-bold">LOCATION / ASSIGNEE</th>
                            <th class="py-3 px-4 font-bold text-center">STATUS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($assets as $asset)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-emerald-800">{{ $asset->asset_code }}</td>
                                <td class="py-3.5 px-4 font-semibold text-slate-900">
                                    {{ $asset->name }}
                                    <div class="text-[10px] text-slate-400 font-mono">SN: {{ $asset->serial_no ?? 'N/A' }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-700">
                                        {{ $asset->category }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $asset->purchase_date->format('d M Y') }}</td>
                                <td class="py-3.5 px-4 text-right font-medium text-slate-800">
                                    KES {{ number_format($asset->purchase_cost, 2) }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-medium text-rose-600">
                                    KES {{ number_format($asset->accumulated_depreciation, 2) }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-black text-emerald-700">
                                    KES {{ number_format($asset->current_book_value, 2) }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    <div>{{ $asset->location }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $asset->assigned_to ?? 'General Plant' }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        {{ $asset->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-8 text-center text-slate-400">No fixed assets registered in the system yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL: REGISTER NEW ASSET -->
        <div x-cloak x-show="openAssetModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl space-y-4 border border-slate-100" @click.away="openAssetModal = false">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-black text-slate-900">Register New Fixed Asset</h3>
                    <button type="button" @click="openAssetModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                </div>

                <form method="POST" action="{{ route('assets.store') }}" class="space-y-3 text-xs">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Asset Tag / Code</label>
                            <input type="text" name="asset_code" required placeholder="e.g. FA-WTP-002"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Asset Category</label>
                            <select name="category" class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                                <option value="Water Treatment & RO Plant">Water Treatment & RO Plant</option>
                                <option value="Bottling & Packaging Line">Bottling & Packaging Line</option>
                                <option value="Motor Vehicles & Trucks">Motor Vehicles & Trucks</option>
                                <option value="Field Dispensers (Leased/Rented)">Field Dispensers (Leased/Rented)</option>
                                <option value="Office Equipment & IT">Office Equipment & IT</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Asset Name / Description</label>
                        <input type="text" name="name" required placeholder="e.g. Industrial Ozonator High Output 50g/h"
                               class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Serial Number</label>
                            <input type="text" name="serial_no" placeholder="e.g. OZ-2026-9021"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Purchase Date</label>
                            <input type="date" name="purchase_date" required value="{{ date('Y-m-d') }}"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Purchase Cost (KES)</label>
                            <input type="number" step="0.01" name="purchase_cost" required placeholder="e.g. 450000"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Salvage Value (KES)</label>
                            <input type="number" step="0.01" name="salvage_value" value="0"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Useful Life (Years)</label>
                            <input type="number" name="useful_life_years" required value="5" min="1"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Depreciation Method</label>
                            <select name="depreciation_method" class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                                <option value="Straight-Line">Straight-Line</option>
                                <option value="Reducing Balance">Reducing Balance</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Annual Depr. Rate (%)</label>
                            <input type="number" step="0.01" name="annual_depreciation_rate" value="20.0"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Plant Location</label>
                            <input type="text" name="location" value="Factory Main Hall" required
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Assigned Custodian / Tech</label>
                            <input type="text" name="assigned_to" placeholder="e.g. Chief Plant Engineer"
                                   class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Status</label>
                        <select name="status" class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs focus:border-emerald-500">
                            <option value="Operational">Operational</option>
                            <option value="Under Maintenance">Under Maintenance</option>
                            <option value="Leased to Client">Leased to Client</option>
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="openAssetModal = false" class="px-4 py-2 rounded-xl text-slate-600 bg-slate-100 hover:bg-slate-200 font-bold">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black shadow-md shadow-emerald-600/20">
                            Save Fixed Asset
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layouts.app>
