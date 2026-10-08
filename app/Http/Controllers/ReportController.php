<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    // 1. General Ledger
    public function generalLedger(Request $request): View
    {
        $startDate = $request->query('start_date', now()->startOfYear()->toDateString());
        $endDate = $request->query('end_date', now()->toDateString());

        $entries = JournalEntry::with(['lines.account'])
            ->whereBetween('entry_date', [$startDate, $endDate])
            ->latest()
            ->paginate(20);

        $totalDebits = JournalEntry::whereBetween('entry_date', [$startDate, $endDate])->sum('total_debit');
        $totalCredits = JournalEntry::whereBetween('entry_date', [$startDate, $endDate])->sum('total_credit');

        return view('reports.general-ledger', compact('entries', 'totalDebits', 'totalCredits', 'startDate', 'endDate'));
    }

    // 2. Accounts Ledgers
    public function accountsLedger(Request $request): View
    {
        $accounts = ChartOfAccount::with(['journalLines.entry'])->orderBy('code')->get();
        $selectedAccountId = $request->query('account_id', $accounts->first()->id ?? 1);
        $selectedAccount = ChartOfAccount::with(['journalLines.entry'])->find($selectedAccountId) ?? $accounts->first();

        return view('reports.accounts-ledger', compact('accounts', 'selectedAccount'));
    }

    // 3. Trial Balance
    public function trialBalance(): View
    {
        $accounts = ChartOfAccount::orderBy('code')->get();

        $debitTotal = 0;
        $creditTotal = 0;

        $rows = $accounts->map(function ($acct) use (&$debitTotal, &$creditTotal) {
            $isDebit = $acct->normal_balance === 'Debit';
            $debit = $isDebit ? $acct->current_balance : 0;
            $credit = ! $isDebit ? $acct->current_balance : 0;

            $debitTotal += $debit;
            $creditTotal += $credit;

            return [
                'code' => $acct->code,
                'name' => $acct->name,
                'type' => $acct->type,
                'debit' => $debit,
                'credit' => $credit,
            ];
        });

        $isBalanced = round($debitTotal, 2) === round($creditTotal, 2);

        return view('reports.trial-balance', compact('rows', 'debitTotal', 'creditTotal', 'isBalanced'));
    }

    // 4. Income Statement (P&L)
    public function incomeStatement(Request $request): View
    {
        $startDate = $request->query('start_date', now()->startOfYear()->toDateString());
        $endDate = $request->query('end_date', now()->toDateString());

        $revenues = ChartOfAccount::where('type', 'Revenue')->get();
        $expenses = ChartOfAccount::where('type', 'Expense')->get();

        $totalRevenue = $revenues->sum('current_balance');
        $cogs = $expenses->where('sub_category', 'Direct Cost')->sum('current_balance');
        $grossProfit = $totalRevenue - $cogs;

        $operatingExpenses = $expenses->where('sub_category', 'Admin Expense');
        $totalOperatingExpenses = $operatingExpenses->sum('current_balance');

        $netProfit = $grossProfit - $totalOperatingExpenses;

        return view('reports.income-statement', compact(
            'revenues',
            'totalRevenue',
            'cogs',
            'grossProfit',
            'operatingExpenses',
            'totalOperatingExpenses',
            'netProfit',
            'startDate',
            'endDate'
        ));
    }

    // 5. Balance Sheet
    public function balanceSheet(): View
    {
        $assetAccounts = ChartOfAccount::where('type', 'Asset')->get();
        $liabilityAccounts = ChartOfAccount::where('type', 'Liability')->get();
        $equityAccounts = ChartOfAccount::where('type', 'Equity')->get();

        $currentAssets = $assetAccounts->where('sub_category', 'Current Asset');
        $fixedAssets = $assetAccounts->where('sub_category', 'Fixed Asset');

        $totalCurrentAssets = $currentAssets->sum('current_balance');
        $totalFixedAssets = $fixedAssets->sum('current_balance');
        $totalAssets = $totalCurrentAssets + $totalFixedAssets;

        $totalLiabilities = $liabilityAccounts->sum('current_balance');
        $totalEquity = $equityAccounts->sum('current_balance');
        $totalLiabilitiesAndEquity = $totalLiabilities + $totalEquity;

        return view('reports.balance-sheet', compact(
            'currentAssets',
            'fixedAssets',
            'totalCurrentAssets',
            'totalFixedAssets',
            'totalAssets',
            'liabilityAccounts',
            'totalLiabilities',
            'equityAccounts',
            'totalEquity',
            'totalLiabilitiesAndEquity'
        ));
    }
}
