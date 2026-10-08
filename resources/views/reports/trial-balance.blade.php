@extends('layouts.app')

@section('title', 'Trial Balance - QUICKSAVE AGENCIES LTD')
@section('page_title', 'Trial Balance Statement')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Periodic Trial Balance Worksheet</h2>
            <p class="text-xs text-gray-500 mt-1">Summary verification that total debit balances equal total credit balances across all active chart of accounts heads.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-4 py-2 border border-gray-200 text-gray-700 bg-white hover:bg-gray-50 text-xs font-semibold rounded-xl flex items-center gap-1.5 shadow-sm transition">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print Trial Balance
            </button>
            <a href="{{ route('reports.income-statement') }}" class="px-4 py-2 bg-emerald-50 text-emerald-800 hover:bg-emerald-100 text-xs font-semibold rounded-xl flex items-center gap-1.5 transition">
                Income Statement &rarr;
            </a>
        </div>
    </div>

    <!-- Balance Verification Banner -->
    <div class="p-5 rounded-2xl border {{ $isBalanced ? 'bg-emerald-50 border-emerald-200' : 'bg-rose-50 border-rose-200' }} flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl {{ $isBalanced ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white' }} flex items-center justify-center font-bold">
                @if($isBalanced)
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                @else
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                @endif
            </div>
            <div>
                <h3 class="font-bold {{ $isBalanced ? 'text-emerald-900' : 'text-rose-900' }} text-sm">
                    {{ $isBalanced ? 'Trial Balance is Fully in Equilibrium' : 'Out of Balance Warning' }}
                </h3>
                <p class="text-xs {{ $isBalanced ? 'text-emerald-700' : 'text-rose-700' }}">
                    {{ $isBalanced ? 'Debits and Credits are equivalent at KES ' . number_format($debitTotal, 2) : 'Variance of KES ' . number_format(abs($debitTotal - $creditTotal), 2) . ' detected between debits and credits.' }}
                </p>
            </div>
        </div>
        <div class="text-right text-xs">
            <div class="text-gray-500">As of:</div>
            <div class="font-bold text-gray-900">{{ now()->format('d M Y, H:i') }}</div>
        </div>
    </div>

    <!-- Trial Balance Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-700">
                <thead class="bg-gray-50 text-gray-500 uppercase text-[10px] tracking-wider font-semibold">
                    <tr>
                        <th class="px-6 py-3.5">Account Code</th>
                        <th class="px-6 py-3.5">Account Head Description</th>
                        <th class="px-6 py-3.5">Classification</th>
                        <th class="px-6 py-3.5 text-right font-bold text-gray-800">Debit (KES)</th>
                        <th class="px-6 py-3.5 text-right font-bold text-gray-800">Credit (KES)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($rows as $row)
                        <tr class="hover:bg-gray-50/70 transition">
                            <td class="px-6 py-3.5 font-mono font-bold text-emerald-800">{{ $row['code'] }}</td>
                            <td class="px-6 py-3.5 font-semibold text-gray-900">{{ $row['name'] }}</td>
                            <td class="px-6 py-3.5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold
                                    @if($row['type'] === 'Asset') bg-blue-50 text-blue-700
                                    @elseif($row['type'] === 'Liability') bg-amber-50 text-amber-700
                                    @elseif($row['type'] === 'Equity') bg-purple-50 text-purple-700
                                    @elseif($row['type'] === 'Revenue') bg-emerald-50 text-emerald-700
                                    @else bg-rose-50 text-rose-700
                                    @endif
                                ">
                                    {{ $row['type'] }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 text-right font-medium {{ $row['debit'] > 0 ? 'text-gray-900 font-bold' : 'text-gray-300' }}">
                                {{ $row['debit'] > 0 ? number_format($row['debit'], 2) : '—' }}
                            </td>
                            <td class="px-6 py-3.5 text-right font-medium {{ $row['credit'] > 0 ? 'text-gray-900 font-bold' : 'text-gray-300' }}">
                                {{ $row['credit'] > 0 ? number_format($row['credit'], 2) : '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50/90 font-black text-gray-900 border-t-2 border-gray-300 text-sm">
                    <tr>
                        <td colspan="3" class="px-6 py-4 uppercase text-xs tracking-wider text-gray-700">Total Trial Balance:</td>
                        <td class="px-6 py-4 text-right text-emerald-800">KES {{ number_format($debitTotal, 2) }}</td>
                        <td class="px-6 py-4 text-right text-emerald-800">KES {{ number_format($creditTotal, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
