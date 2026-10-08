@extends('layouts.app')

@section('title', 'General Ledger - QUICKSAVE AGENCIES LTD')
@section('page_title', 'General Ledger Journal')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Master General Ledger Journal</h2>
            <p class="text-xs text-gray-500 mt-1">Audit log of all double-entry debits, credits, and financial transactions across the firm.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-4 py-2 border border-gray-200 text-gray-700 bg-white hover:bg-gray-50 text-xs font-semibold rounded-xl flex items-center gap-1.5 shadow-sm transition">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print General Ledger
            </button>
            <a href="{{ route('reports.trial-balance') }}" class="px-4 py-2 bg-emerald-50 text-emerald-800 hover:bg-emerald-100 text-xs font-semibold rounded-xl flex items-center gap-1.5 transition">
                Trial Balance &rarr;
            </a>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
        <form method="GET" action="{{ route('reports.general-ledger') }}" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-2 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-2 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>
            <button type="submit" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-xl transition shadow-sm">
                Filter Journal Entries
            </button>
            <a href="{{ route('reports.general-ledger') }}" class="px-4 py-2 text-xs text-gray-500 hover:text-gray-800">
                Reset
            </a>
        </form>
    </div>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Debits Posted</span>
            <div class="text-2xl font-black text-emerald-800 mt-2">KES {{ number_format($totalDebits, 2) }}</div>
            <div class="text-[11px] text-gray-400 mt-1">Period total debits</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Credits Posted</span>
            <div class="text-2xl font-black text-emerald-800 mt-2">KES {{ number_format($totalCredits, 2) }}</div>
            <div class="text-[11px] text-gray-400 mt-1">Period total credits</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Journal Equation Status</span>
            <div class="mt-2 flex items-center gap-2">
                @if(round($totalDebits, 2) === round($totalCredits, 2))
                    <span class="px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-lg border border-emerald-200 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        PERFECTLY BALANCED
                    </span>
                @else
                    <span class="px-3 py-1 bg-rose-50 text-rose-700 text-xs font-bold rounded-lg border border-rose-200">
                        VARIANCE: KES {{ number_format(abs($totalDebits - $totalCredits), 2) }}
                    </span>
                @endif
            </div>
            <div class="text-[11px] text-gray-400 mt-1">Debit equals Credit verification</div>
        </div>
    </div>

    <!-- Journal Entries Listing -->
    <div class="space-y-4">
        @forelse($entries as $entry)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-3.5 bg-gray-50/70 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-4">
                        <span class="font-mono font-bold text-emerald-800 text-sm">{{ $entry->entry_no }}</span>
                        <span class="text-gray-500">{{ \Carbon\Carbon::parse($entry->entry_date)->format('d M Y') }}</span>
                        @if($entry->reference_no)
                            <span class="px-2 py-0.5 bg-white border border-gray-200 rounded font-mono text-[10px] text-gray-600">Ref: {{ $entry->reference_no }}</span>
                        @endif
                    </div>
                    <div class="font-medium text-gray-700">
                        {{ $entry->description }}
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-gray-700">
                        <thead class="bg-white text-gray-400 uppercase text-[10px] tracking-wider border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-2.5">Account Code & Name</th>
                                <th class="px-6 py-2.5">Line Description</th>
                                <th class="px-6 py-2.5 text-right">Debit (KES)</th>
                                <th class="px-6 py-2.5 text-right">Credit (KES)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($entry->lines as $line)
                                <tr class="hover:bg-gray-50/50">
                                    <td class="px-6 py-3">
                                        <span class="font-mono font-semibold text-gray-900">{{ $line->account?->code }}</span>
                                        <span class="text-gray-600 ml-2">{{ $line->account?->name }}</span>
                                    </td>
                                    <td class="px-6 py-3 text-gray-500">{{ $line->description ?? '—' }}</td>
                                    <td class="px-6 py-3 text-right font-medium {{ $line->type === 'Debit' ? 'text-gray-900 font-bold' : 'text-gray-300' }}">
                                        {{ $line->type === 'Debit' ? number_format($line->amount, 2) : '—' }}
                                    </td>
                                    <td class="px-6 py-3 text-right font-medium {{ $line->type === 'Credit' ? 'text-gray-900 font-bold' : 'text-gray-300' }}">
                                        {{ $line->type === 'Credit' ? number_format($line->amount, 2) : '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50/40 text-xs font-bold text-gray-800 border-t border-gray-100">
                            <tr>
                                <td colspan="2" class="px-6 py-2 text-right uppercase text-[10px] text-gray-400">Entry Total:</td>
                                <td class="px-6 py-2 text-right text-emerald-800">{{ number_format($entry->total_debit, 2) }}</td>
                                <td class="px-6 py-2 text-right text-emerald-800">{{ number_format($entry->total_credit, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @empty
            <div class="bg-white p-12 text-center rounded-2xl border border-gray-100 shadow-sm text-gray-400">
                No journal entries found in this date range.
            </div>
        @endforelse

        <div class="pt-2">
            {{ $entries->links() }}
        </div>
    </div>
</div>
@endsection
