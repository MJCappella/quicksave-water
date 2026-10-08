@extends('layouts.app')

@section('title', 'Balance Sheet - QUICKSAVE AGENCIES LTD')
@section('page_title', 'Statement of Financial Position')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Statement of Financial Position (Balance Sheet)</h2>
            <p class="text-xs text-gray-500 mt-1">Comprehensive audit of company assets, current liabilities, long-term borrowings, and shareholder equity.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-4 py-2 border border-gray-200 text-gray-700 bg-white hover:bg-gray-50 text-xs font-semibold rounded-xl flex items-center gap-1.5 shadow-sm transition">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print Balance Sheet
            </button>
            <a href="{{ route('reports.income-statement') }}" class="px-4 py-2 bg-emerald-50 text-emerald-800 hover:bg-emerald-100 text-xs font-semibold rounded-xl flex items-center gap-1.5 transition">
                &larr; Income Statement
            </a>
        </div>
    </div>

    <!-- Balance Status Banner -->
    @php
        $diff = abs($totalAssets - $totalLiabilitiesAndEquity);
        $isBalanced = $diff < 1.0;
    @endphp
    <div class="p-5 rounded-2xl border {{ $isBalanced ? 'bg-emerald-50 border-emerald-200' : 'bg-amber-50 border-amber-200' }} flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl {{ $isBalanced ? 'bg-emerald-600 text-white' : 'bg-amber-600 text-white' }} flex items-center justify-center font-bold">
                @if($isBalanced)
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                @else
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                @endif
            </div>
            <div>
                <h3 class="font-bold {{ $isBalanced ? 'text-emerald-900' : 'text-amber-900' }} text-sm">
                    {{ $isBalanced ? 'Fundamental Accounting Equation Satisfied: Assets = Liabilities + Equity' : 'Equation Variance Alert' }}
                </h3>
                <p class="text-xs {{ $isBalanced ? 'text-emerald-700' : 'text-amber-700' }}">
                    Total Assets (KES {{ number_format($totalAssets, 2) }}) vs Total Liabilities & Equity (KES {{ number_format($totalLiabilitiesAndEquity, 2) }})
                </p>
            </div>
        </div>
        <div class="text-right text-xs">
            <div class="text-gray-500">As at:</div>
            <div class="font-bold text-gray-900">{{ now()->format('d F Y') }}</div>
        </div>
    </div>

    <!-- Formal Statement Layout -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 max-w-4xl mx-auto space-y-8">
        <div class="text-center border-b border-gray-200 pb-6">
            <div class="text-xs font-black uppercase tracking-widest text-emerald-700">QUICKSAVE AGENCIES LTD</div>
            <h1 class="text-xl font-black text-gray-900 mt-1">STATEMENT OF FINANCIAL POSITION</h1>
            <p class="text-xs text-gray-500 mt-1">As at {{ now()->format('d F Y') }}</p>
        </div>

        <div class="space-y-8 text-xs text-gray-800">
            <!-- SECTION 1: ASSETS -->
            <div class="space-y-4">
                <div class="flex justify-between items-center py-2 font-bold text-sm text-emerald-900 border-b-2 border-emerald-800">
                    <span class="uppercase tracking-wider">ASSETS</span>
                    <span>KES</span>
                </div>

                <!-- Current Assets -->
                <div>
                    <h4 class="font-bold text-gray-700 uppercase tracking-wider text-[11px] mb-1">Current Assets</h4>
                    <div class="divide-y divide-gray-100">
                        @foreach($currentAssets as $ca)
                            <div class="flex justify-between py-2 pl-4">
                                <span class="text-gray-700">{{ $ca->code }} - {{ $ca->name }}</span>
                                <span class="font-medium">KES {{ number_format($ca->current_balance, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex justify-between py-2 font-semibold text-gray-800 bg-gray-50/70 px-4 rounded-lg mt-1">
                        <span>Total Current Assets</span>
                        <span>KES {{ number_format($totalCurrentAssets, 2) }}</span>
                    </div>
                </div>

                <!-- Non-Current / Fixed Assets -->
                <div>
                    <h4 class="font-bold text-gray-700 uppercase tracking-wider text-[11px] mb-1">Non-Current (Fixed) Assets</h4>
                    <div class="divide-y divide-gray-100">
                        @foreach($fixedAssets as $fa)
                            <div class="flex justify-between py-2 pl-4">
                                <span class="text-gray-700">{{ $fa->code }} - {{ $fa->name }}</span>
                                <span class="font-medium">KES {{ number_format($fa->current_balance, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex justify-between py-2 font-semibold text-gray-800 bg-gray-50/70 px-4 rounded-lg mt-1">
                        <span>Total Fixed Assets</span>
                        <span>KES {{ number_format($totalFixedAssets, 2) }}</span>
                    </div>
                </div>

                <!-- Total Assets -->
                <div class="flex justify-between py-3 font-black text-sm text-emerald-900 bg-emerald-50 px-5 rounded-xl border border-emerald-200">
                    <span class="uppercase tracking-wider">TOTAL ASSETS</span>
                    <span>KES {{ number_format($totalAssets, 2) }}</span>
                </div>
            </div>

            <!-- SECTION 2: LIABILITIES & EQUITY -->
            <div class="space-y-4 pt-4 border-t-2 border-gray-200">
                <div class="flex justify-between items-center py-2 font-bold text-sm text-teal-900 border-b-2 border-teal-800">
                    <span class="uppercase tracking-wider">LIABILITIES & SHAREHOLDER EQUITY</span>
                    <span>KES</span>
                </div>

                <!-- Liabilities -->
                <div>
                    <h4 class="font-bold text-gray-700 uppercase tracking-wider text-[11px] mb-1">Current Liabilities</h4>
                    <div class="divide-y divide-gray-100">
                        @foreach($liabilityAccounts as $liab)
                            <div class="flex justify-between py-2 pl-4">
                                <span class="text-gray-700">{{ $liab->code }} - {{ $liab->name }}</span>
                                <span class="font-medium">KES {{ number_format($liab->current_balance, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex justify-between py-2 font-semibold text-gray-800 bg-gray-50/70 px-4 rounded-lg mt-1">
                        <span>Total Liabilities</span>
                        <span>KES {{ number_format($totalLiabilities, 2) }}</span>
                    </div>
                </div>

                <!-- Equity -->
                <div>
                    <h4 class="font-bold text-gray-700 uppercase tracking-wider text-[11px] mb-1">Owners Equity & Retained Earnings</h4>
                    <div class="divide-y divide-gray-100">
                        @foreach($equityAccounts as $eq)
                            <div class="flex justify-between py-2 pl-4">
                                <span class="text-gray-700">{{ $eq->code }} - {{ $eq->name }}</span>
                                <span class="font-medium">KES {{ number_format($eq->current_balance, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex justify-between py-2 font-semibold text-gray-800 bg-gray-50/70 px-4 rounded-lg mt-1">
                        <span>Total Equity</span>
                        <span>KES {{ number_format($totalEquity, 2) }}</span>
                    </div>
                </div>

                <!-- Total Liabilities and Equity -->
                <div class="flex justify-between py-3.5 font-black text-sm text-white bg-gradient-to-r from-emerald-800 to-teal-800 px-5 rounded-xl shadow-sm">
                    <span class="uppercase tracking-wider">TOTAL LIABILITIES & EQUITY</span>
                    <span>KES {{ number_format($totalLiabilitiesAndEquity, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
