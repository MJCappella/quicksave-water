@extends('layouts.app')

@section('title', 'Income Statement (P&L) - QUICKSAVE AGENCIES LTD')
@section('page_title', 'Statement of Comprehensive Income')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Profit & Loss / Income Statement</h2>
            <p class="text-xs text-gray-500 mt-1">Financial performance statement detailing operating revenue, direct cost of bottled water goods sold, gross margin, administrative expenses, and net profit.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-4 py-2 border border-gray-200 text-gray-700 bg-white hover:bg-gray-50 text-xs font-semibold rounded-xl flex items-center gap-1.5 shadow-sm transition">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print Income Statement
            </button>
            <a href="{{ route('reports.balance-sheet') }}" class="px-4 py-2 bg-emerald-50 text-emerald-800 hover:bg-emerald-100 text-xs font-semibold rounded-xl flex items-center gap-1.5 transition">
                Balance Sheet &rarr;
            </a>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
        <form method="GET" action="{{ route('reports.income-statement') }}" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-2 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-2 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>
            <button type="submit" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-xl transition shadow-sm">
                Generate Statement
            </button>
            <a href="{{ route('reports.income-statement') }}" class="px-4 py-2 text-xs text-gray-500 hover:text-gray-800">
                Reset
            </a>
        </form>
    </div>

    <!-- Executive Highlights Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Gross Operating Revenue</span>
            <div class="text-2xl font-black text-gray-900 mt-2">KES {{ number_format($totalRevenue, 2) }}</div>
            <div class="text-[11px] text-emerald-600 mt-1">From commercial & retail POS sales</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Cost of Goods Sold (COGS)</span>
            <div class="text-2xl font-black text-gray-700 mt-2">KES {{ number_format($cogs, 2) }}</div>
            <div class="text-[11px] text-gray-400 mt-1">Preforms, caps, bottles, chemicals</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Gross Profit Margin</span>
            <div class="text-2xl font-black text-emerald-700 mt-2">KES {{ number_format($grossProfit, 2) }}</div>
            <div class="text-[11px] text-emerald-600 mt-1">
                Margin: {{ $totalRevenue > 0 ? number_format(($grossProfit / $totalRevenue) * 100, 1) : 0 }}%
            </div>
        </div>

        <div class="bg-gradient-to-br from-emerald-800 to-teal-900 text-white p-5 rounded-2xl shadow-sm">
            <span class="text-xs text-emerald-200 uppercase tracking-wider font-semibold">Net Operating Income</span>
            <div class="text-2xl font-black mt-2">KES {{ number_format($netProfit, 2) }}</div>
            <div class="text-[11px] text-emerald-300 mt-1">
                Net Margin: {{ $totalRevenue > 0 ? number_format(($netProfit / $totalRevenue) * 100, 1) : 0 }}%
            </div>
        </div>
    </div>

    <!-- Formal Income Statement Layout -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 max-w-4xl mx-auto space-y-6">
        <div class="text-center border-b border-gray-200 pb-6">
            <div class="text-xs font-black uppercase tracking-widest text-emerald-700">QUICKSAVE AGENCIES LTD</div>
            <h1 class="text-xl font-black text-gray-900 mt-1">STATEMENT OF COMPREHENSIVE INCOME (PROFIT & LOSS)</h1>
            <p class="text-xs text-gray-500 mt-1">For the period ending {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}</p>
        </div>

        <div class="space-y-6 text-xs text-gray-800">
            <!-- 1. Revenue -->
            <div>
                <div class="flex justify-between items-center py-2 font-bold text-sm text-gray-900 border-b border-gray-200">
                    <span class="uppercase tracking-wider">Operating Revenue</span>
                    <span>KES</span>
                </div>
                <div class="divide-y divide-gray-100 mt-1">
                    @foreach($revenues as $rev)
                        <div class="flex justify-between py-2 pl-4">
                            <span class="text-gray-700">{{ $rev->code }} - {{ $rev->name }}</span>
                            <span class="font-medium">KES {{ number_format($rev->current_balance, 2) }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="flex justify-between py-2.5 font-bold text-gray-900 bg-gray-50/80 px-4 rounded-lg mt-2">
                    <span>Total Operating Revenue</span>
                    <span class="text-emerald-800 font-black">KES {{ number_format($totalRevenue, 2) }}</span>
                </div>
            </div>

            <!-- 2. Cost of Goods Sold -->
            <div>
                <div class="flex justify-between items-center py-2 font-bold text-sm text-gray-900 border-b border-gray-200">
                    <span class="uppercase tracking-wider">Cost of Goods Sold (Direct Costs)</span>
                    <span>KES</span>
                </div>
                <div class="divide-y divide-gray-100 mt-1">
                    <div class="flex justify-between py-2 pl-4">
                        <span class="text-gray-700">Direct Packaging Materials & Water Treatment Supplies</span>
                        <span class="font-medium">KES {{ number_format($cogs, 2) }}</span>
                    </div>
                </div>
                <div class="flex justify-between py-2.5 font-bold text-gray-900 bg-gray-50/80 px-4 rounded-lg mt-2">
                    <span>Total Cost of Sales</span>
                    <span class="text-gray-900 font-bold">KES {{ number_format($cogs, 2) }}</span>
                </div>
            </div>

            <!-- 3. Gross Profit -->
            <div class="flex justify-between py-3 font-black text-sm text-emerald-900 bg-emerald-50 px-5 rounded-xl border border-emerald-200">
                <span class="uppercase tracking-wider">GROSS PROFIT</span>
                <span>KES {{ number_format($grossProfit, 2) }}</span>
            </div>

            <!-- 4. Operating Expenses -->
            <div>
                <div class="flex justify-between items-center py-2 font-bold text-sm text-gray-900 border-b border-gray-200">
                    <span class="uppercase tracking-wider">Operating & Administrative Expenses</span>
                    <span>KES</span>
                </div>
                <div class="divide-y divide-gray-100 mt-1">
                    @foreach($operatingExpenses as $exp)
                        <div class="flex justify-between py-2 pl-4">
                            <span class="text-gray-700">{{ $exp->code }} - {{ $exp->name }}</span>
                            <span class="font-medium">KES {{ number_format($exp->current_balance, 2) }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="flex justify-between py-2.5 font-bold text-gray-900 bg-gray-50/80 px-4 rounded-lg mt-2">
                    <span>Total Operating Expenses</span>
                    <span class="text-rose-700 font-bold">KES {{ number_format($totalOperatingExpenses, 2) }}</span>
                </div>
            </div>

            <!-- 5. Net Operating Profit -->
            <div class="flex justify-between py-4 font-black text-base text-white bg-gradient-to-r from-emerald-800 to-teal-800 px-6 rounded-xl shadow-sm">
                <span class="uppercase tracking-wider">NET OPERATING PROFIT / (LOSS)</span>
                <span>KES {{ number_format($netProfit, 2) }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
