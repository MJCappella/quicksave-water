@extends('layouts.app')

@section('title', 'Accounts Ledger - QUICKSAVE AGENCIES LTD')
@section('page_title', 'Individual Accounts Ledger')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Individual Accounts Subsidiary Ledger</h2>
            <p class="text-xs text-gray-500 mt-1">Detailed transaction trail, historical debit/credit postings, and running balances per account head.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-4 py-2 border border-gray-200 text-gray-700 bg-white hover:bg-gray-50 text-xs font-semibold rounded-xl flex items-center gap-1.5 shadow-sm transition">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print Ledger
            </button>
            <a href="{{ route('reports.general-ledger') }}" class="px-4 py-2 bg-emerald-50 text-emerald-800 hover:bg-emerald-100 text-xs font-semibold rounded-xl flex items-center gap-1.5 transition">
                View General Ledger &rarr;
            </a>
        </div>
    </div>

    <!-- Account Selector Bar -->
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
        <form method="GET" action="{{ route('reports.accounts-ledger') }}" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[300px]">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Select Chart of Account Head</label>
                <select name="account_id" onchange="this.form.submit()" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none font-medium">
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ $selectedAccount && $selectedAccount->id == $acc->id ? 'selected' : '' }}>
                            {{ $acc->code }} - {{ $acc->name }} ({{ $acc->type }} / {{ $acc->sub_category }}) - Bal: KES {{ number_format($acc->current_balance, 2) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-xl transition shadow-sm">
                View Account Ledger
            </button>
        </form>
    </div>

    @if($selectedAccount)
    <!-- Active Account Summary Card -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
        <div class="bg-gradient-to-br from-emerald-800 to-teal-900 text-white p-5 rounded-2xl shadow-sm">
            <div class="text-xs text-emerald-200 uppercase tracking-wider font-semibold">Current Account Balance</div>
            <div class="text-2xl font-black mt-2">KES {{ number_format($selectedAccount->current_balance, 2) }}</div>
            <div class="text-[11px] text-emerald-300 mt-1">Normal Balance: {{ $selectedAccount->normal_balance }}</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Account Code & Name</span>
            <div class="text-lg font-bold text-gray-900 mt-2">{{ $selectedAccount->code }}</div>
            <div class="text-xs text-gray-600 font-medium truncate">{{ $selectedAccount->name }}</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Classification</span>
            <div class="text-base font-bold text-gray-900 mt-2">{{ $selectedAccount->type }}</div>
            <div class="text-xs text-gray-500 mt-1">{{ $selectedAccount->sub_category }}</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Ledger Postings</span>
            <div class="text-2xl font-black text-emerald-700 mt-2">{{ $selectedAccount->journalLines->count() }}</div>
            <div class="text-[11px] text-gray-400 mt-1">Verified double entries</div>
        </div>
    </div>

    <!-- Ledger Postings Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-900 text-sm">Ledger Entries for {{ $selectedAccount->name }}</h3>
            <span class="text-xs font-mono text-gray-500">Account Code: {{ $selectedAccount->code }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-700">
                <thead class="bg-gray-50 text-gray-500 uppercase text-[10px] tracking-wider font-semibold">
                    <tr>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Journal Entry #</th>
                        <th class="px-6 py-3">Ref #</th>
                        <th class="px-6 py-3">Particulars / Narration</th>
                        <th class="px-6 py-3 text-right">Debit (KES)</th>
                        <th class="px-6 py-3 text-right">Credit (KES)</th>
                        <th class="px-6 py-3 text-right">Running Balance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php 
                        $running = 0;
                        $isDebitNorm = $selectedAccount->normal_balance === 'Debit';
                    @endphp
                    @forelse($selectedAccount->journalLines as $line)
                        @php
                            if ($line->type === 'Debit') {
                                $running += $isDebitNorm ? $line->amount : -$line->amount;
                            } else {
                                $running += $isDebitNorm ? -$line->amount : $line->amount;
                            }
                        @endphp
                        <tr class="hover:bg-gray-50/70 transition">
                            <td class="px-6 py-4 text-gray-600">{{ \Carbon\Carbon::parse($line->entry?->entry_date ?? now())->format('d M Y') }}</td>
                            <td class="px-6 py-4 font-mono font-bold text-emerald-800">{{ $line->entry?->entry_no ?? '—' }}</td>
                            <td class="px-6 py-4 font-mono text-gray-500">{{ $line->entry?->reference_no ?? '—' }}</td>
                            <td class="px-6 py-4 text-gray-800">
                                <div class="font-medium">{{ $line->description ?? $line->entry?->description }}</div>
                            </td>
                            <td class="px-6 py-4 text-right font-medium {{ $line->type === 'Debit' ? 'text-gray-900 font-bold' : 'text-gray-400' }}">
                                {{ $line->type === 'Debit' ? 'KES ' . number_format($line->amount, 2) : '—' }}
                            </td>
                            <td class="px-6 py-4 text-right font-medium {{ $line->type === 'Credit' ? 'text-gray-900 font-bold' : 'text-gray-400' }}">
                                {{ $line->type === 'Credit' ? 'KES ' . number_format($line->amount, 2) : '—' }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-emerald-700">
                                KES {{ number_format($running, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                No journal line items posted to this account yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50/80 font-bold text-gray-900 border-t-2 border-gray-200">
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-right uppercase text-[11px] tracking-wider text-gray-600">Total Account Balance:</td>
                        <td colspan="3" class="px-6 py-4 text-right text-emerald-800 text-sm">KES {{ number_format($selectedAccount->current_balance, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
