<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\BankReconciliation;
use App\Models\CashTransaction;
use App\Models\Customer;
use App\Models\MpesaTransaction;
use App\Models\SalesInvoice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BankingController extends Controller
{
    // 1. Petty Cashbook
    public function pettyCash(): View
    {
        $pettyAccount = BankAccount::where('account_type', 'Petty Cash')->first();
        $transactions = CashTransaction::where('bank_account_id', $pettyAccount?->id)->latest()->paginate(15);

        $totalInflow = CashTransaction::where('bank_account_id', $pettyAccount?->id)->where('type', 'INFLOW')->sum('amount');
        $totalOutflow = CashTransaction::where('bank_account_id', $pettyAccount?->id)->where('type', 'OUTFLOW')->sum('amount');
        $currentBalance = $pettyAccount?->current_balance ?? 0;

        return view('banking.petty-cash', compact('pettyAccount', 'transactions', 'totalInflow', 'totalOutflow', 'currentBalance'));
    }

    public function storePettyCash(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'txn_date' => 'required|date',
            'type' => 'required|string|in:INFLOW,OUTFLOW',
            'category' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'payee_or_payer' => 'required|string|max:255',
            'reference_no' => 'nullable|string|max:100',
            'description' => 'required|string',
        ]);

        $pettyAccount = BankAccount::where('account_type', 'Petty Cash')->firstOrFail();
        $amount = (float) $validated['amount'];

        DB::transaction(function () use ($validated, $pettyAccount, $amount) {
            $txnNo = 'PC-'.date('Y').'-'.str_pad((string) (CashTransaction::where('bank_account_id', $pettyAccount->id)->count() + 1), 4, '0', STR_PAD_LEFT);

            CashTransaction::create([
                'txn_no' => $txnNo,
                'bank_account_id' => $pettyAccount->id,
                'txn_date' => $validated['txn_date'],
                'type' => $validated['type'],
                'category' => $validated['category'],
                'amount' => $amount,
                'payee_or_payer' => $validated['payee_or_payer'],
                'reference_no' => $validated['reference_no'] ?? null,
                'description' => $validated['description'],
                'approved_by' => auth()->user()->name ?? 'Finance Manager',
                'is_reconciled' => true,
            ]);

            if ($validated['type'] === 'INFLOW') {
                $pettyAccount->increment('current_balance', $amount);
            } else {
                $pettyAccount->decrement('current_balance', $amount);
            }
        });

        return redirect()->route('banking.petty-cash')->with('success', 'Petty cash transaction recorded successfully!');
    }

    // 2. Main Cashbook
    public function mainCash(): View
    {
        $mainAccount = BankAccount::where('account_type', 'Bank')->first();
        $transactions = CashTransaction::where('bank_account_id', $mainAccount?->id)->latest()->paginate(15);

        $bankAccounts = BankAccount::all();
        $totalInflow = CashTransaction::where('bank_account_id', $mainAccount?->id)->where('type', 'INFLOW')->sum('amount');
        $totalOutflow = CashTransaction::where('bank_account_id', $mainAccount?->id)->where('type', 'OUTFLOW')->sum('amount');
        $currentBalance = $mainAccount?->current_balance ?? 0;

        return view('banking.main-cash', compact('mainAccount', 'transactions', 'bankAccounts', 'totalInflow', 'totalOutflow', 'currentBalance'));
    }

    public function storeMainCash(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'txn_date' => 'required|date',
            'type' => 'required|string|in:INFLOW,OUTFLOW,CONTRA',
            'category' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'payee_or_payer' => 'required|string|max:255',
            'reference_no' => 'nullable|string|max:100',
            'description' => 'required|string',
        ]);

        $account = BankAccount::findOrFail($validated['bank_account_id']);
        $amount = (float) $validated['amount'];

        DB::transaction(function () use ($validated, $account, $amount) {
            $txnNo = 'MC-'.date('Y').'-'.str_pad((string) (CashTransaction::count() + 1), 4, '0', STR_PAD_LEFT);

            CashTransaction::create([
                'txn_no' => $txnNo,
                'bank_account_id' => $account->id,
                'txn_date' => $validated['txn_date'],
                'type' => $validated['type'],
                'category' => $validated['category'],
                'amount' => $amount,
                'payee_or_payer' => $validated['payee_or_payer'],
                'reference_no' => $validated['reference_no'] ?? null,
                'description' => $validated['description'],
                'approved_by' => auth()->user()->name ?? 'Finance Director',
                'is_reconciled' => true,
            ]);

            if ($validated['type'] === 'INFLOW') {
                $account->increment('current_balance', $amount);
            } else {
                $account->decrement('current_balance', $amount);
            }
        });

        return redirect()->route('banking.main-cash')->with('success', 'Main cashbook entry posted!');
    }

    // 3. M-Pesa Paybill / Till Management
    public function mpesa(Request $request): View
    {
        $selectedType = $request->query('type');
        $query = MpesaTransaction::with('account')->latest();

        if ($selectedType) {
            $query->where('type', $selectedType);
        }

        $transactions = $query->paginate(20);
        $totalCollected = MpesaTransaction::sum('amount');
        $unallocatedCount = MpesaTransaction::where('status', 'Unallocated')->count();
        $unallocatedAmount = MpesaTransaction::where('status', 'Unallocated')->sum('amount');

        $invoices = SalesInvoice::where('balance_due', '>', 0)->get();
        $customers = Customer::where('is_active', true)->get();

        return view('banking.mpesa', compact(
            'transactions',
            'totalCollected',
            'unallocatedCount',
            'unallocatedAmount',
            'selectedType',
            'invoices',
            'customers'
        ));
    }

    public function allocateMpesa(Request $request, MpesaTransaction $transaction): RedirectResponse
    {
        $validated = $request->validate([
            'allocated_to_type' => 'required|string|in:SalesInvoice,Customer',
            'allocated_to_id' => 'required|integer',
        ]);

        DB::transaction(function () use ($transaction, $validated) {
            $transaction->update([
                'status' => 'Allocated',
                'allocated_to_type' => $validated['allocated_to_type'],
                'allocated_to_id' => $validated['allocated_to_id'],
            ]);

            if ($validated['allocated_to_type'] === 'SalesInvoice') {
                $invoice = SalesInvoice::find($validated['allocated_to_id']);
                if ($invoice) {
                    $invoice->increment('paid_amount', $transaction->amount);
                    $newBal = max(0, $invoice->grand_total - $invoice->paid_amount);
                    $invoice->update([
                        'balance_due' => $newBal,
                        'payment_status' => $newBal <= 0 ? 'Paid' : 'Partial',
                    ]);
                    $invoice->customer?->decrement('current_balance', min($invoice->customer->current_balance, $transaction->amount));
                }
            } elseif ($validated['allocated_to_type'] === 'Customer') {
                $customer = Customer::find($validated['allocated_to_id']);
                if ($customer) {
                    $customer->decrement('current_balance', $transaction->amount);
                }
            }
        });

        return redirect()->route('banking.mpesa')->with('success', 'M-Pesa payment allocated successfully!');
    }

    // 4. Bank Reconciliation
    public function reconciliation(): View
    {
        $bankAccount = BankAccount::where('account_type', 'Bank')->first();
        $reconciliation = BankReconciliation::with(['bankAccount', 'lines'])->latest()->first();
        $bankAccounts = BankAccount::where('account_type', 'Bank')->get();

        return view('banking.reconciliation', compact('bankAccount', 'reconciliation', 'bankAccounts'));
    }

    public function storeReconciliation(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'statement_date' => 'required|date',
            'statement_ending_balance' => 'required|numeric',
            'notes' => 'nullable|string',
        ]);

        $account = BankAccount::findOrFail($validated['bank_account_id']);
        $bookBalance = (float) $account->current_balance;
        $statementBalance = (float) $validated['statement_ending_balance'];
        $difference = round($statementBalance - $bookBalance, 2);

        $recNo = 'BRC-'.date('Ym').'-'.str_pad((string) (BankReconciliation::count() + 1), 3, '0', STR_PAD_LEFT);

        BankReconciliation::create([
            'reconciliation_no' => $recNo,
            'bank_account_id' => $account->id,
            'statement_date' => $validated['statement_date'],
            'statement_ending_balance' => $statementBalance,
            'book_balance' => $bookBalance,
            'cleared_balance' => $statementBalance,
            'difference' => $difference,
            'status' => $difference == 0 ? 'Reconciled' : 'In Progress',
            'reconciled_by' => auth()->user()->name ?? 'Finance Manager',
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('banking.reconciliation')->with('success', 'Bank reconciliation statement filed successfully!');
    }
}
