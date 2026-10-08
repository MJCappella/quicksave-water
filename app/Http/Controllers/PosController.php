<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\CashTransaction;
use App\Models\Customer;
use App\Models\CustomerReceipt;
use App\Models\MpesaTransaction;
use App\Models\Product;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\StockMovement;
use App\Models\Store;
use App\Models\StoreInventory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PosController extends Controller
{
    public function index(Request $request): View
    {
        $stores = Store::where('is_active', true)->get();
        $selectedStoreId = $request->query('store_id', $stores->first()->id ?? 1);
        $currentStore = Store::find($selectedStoreId) ?? $stores->first();

        $products = Product::where('is_active', true)
            ->with(['inventories' => function ($q) use ($currentStore) {
                if ($currentStore) {
                    $q->where('store_id', $currentStore->id);
                }
            }])
            ->get();

        $posProducts = $products->map(function ($p) {
            $inv = $p->inventories->first();

            return [
                'id' => $p->id,
                'sku' => $p->sku,
                'name' => $p->name,
                'category' => $p->category,
                'price' => (float) $p->retail_price,
                'stock' => $inv ? (int) $inv->quantity : 0,
            ];
        });

        $categories = $products->pluck('category')->unique()->values();
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $recentSales = SalesInvoice::where('is_pos', true)->with('customer')->latest()->take(8)->get();

        return view('pos.index', compact(
            'stores',
            'currentStore',
            'products',
            'posProducts',
            'categories',
            'customers',
            'recentSales'
        ));
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'customer_id' => 'required|exists:customers,id',
            'payment_method' => 'required|string|in:Cash,Mpesa,Bank,Credit',
            'reference_no' => 'nullable|string|max:100',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $invoiceNo = 'POS-'.date('Y').'-'.str_pad((string) (SalesInvoice::where('is_pos', true)->count() + 1), 5, '0', STR_PAD_LEFT);
            $store = Store::findOrFail($validated['store_id']);
            $customer = Customer::findOrFail($validated['customer_id']);

            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $subtotal += $item['quantity'] * $item['unit_price'];
            }
            $grandTotal = $subtotal;
            $isPaid = $validated['payment_method'] !== 'Credit';

            $invoice = SalesInvoice::create([
                'invoice_no' => $invoiceNo,
                'customer_id' => $customer->id,
                'store_id' => $store->id,
                'sales_order_id' => null,
                'invoice_date' => now()->toDateString(),
                'due_date' => now()->toDateString(),
                'subtotal' => $subtotal,
                'tax_amount' => 0.00,
                'discount_amount' => 0.00,
                'grand_total' => $grandTotal,
                'paid_amount' => $isPaid ? $grandTotal : 0.00,
                'balance_due' => $isPaid ? 0.00 : $grandTotal,
                'payment_method' => $validated['payment_method'],
                'payment_status' => $isPaid ? 'Paid' : 'Unpaid',
                'is_pos' => true,
                'cashier_name' => auth()->user()->name ?? 'James Mwangi',
                'notes' => 'Counter POS Sale. Payment Ref: '.($validated['reference_no'] ?? 'N/A'),
            ]);

            // Create items, adjust inventory & record audit movements
            foreach ($validated['items'] as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);
                $qty = (int) $itemData['quantity'];
                $price = (float) $itemData['unit_price'];
                $lineTotal = $qty * $price;

                SalesInvoiceItem::create([
                    'sales_invoice_id' => $invoice->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'discount' => 0,
                    'tax_amount' => 0,
                    'total' => $lineTotal,
                ]);

                // Store Inventory adjustment
                $inv = StoreInventory::firstOrCreate(
                    ['store_id' => $store->id, 'product_id' => $product->id],
                    ['quantity' => 0]
                );

                $balBefore = $inv->quantity;
                $inv->quantity = max(0, $inv->quantity - $qty);
                $inv->save();

                // Stock Movement Audit Ledger
                StockMovement::create([
                    'product_id' => $product->id,
                    'store_id' => $store->id,
                    'type' => 'SALE',
                    'reference_type' => 'POS',
                    'reference_no' => $invoiceNo,
                    'quantity_change' => -$qty,
                    'balance_before' => $balBefore,
                    'balance_after' => $inv->quantity,
                    'user_name' => auth()->user()->name ?? 'Cashier Counter',
                    'notes' => "POS sale to {$customer->name}",
                ]);
            }

            // Customer balance update if on credit
            if (! $isPaid) {
                $customer->increment('current_balance', $grandTotal);
            }

            // Record Customer Receipt and Banking / Till / Cash transactions
            if ($isPaid) {
                $receiptNo = 'RCP-'.date('Y').'-'.str_pad((string) (CustomerReceipt::count() + 1), 5, '0', STR_PAD_LEFT);
                CustomerReceipt::create([
                    'receipt_no' => $receiptNo,
                    'customer_id' => $customer->id,
                    'sales_invoice_id' => $invoice->id,
                    'receipt_date' => now()->toDateString(),
                    'amount' => $grandTotal,
                    'payment_method' => $validated['payment_method'],
                    'reference_no' => $validated['reference_no'] ?? $invoiceNo,
                    'receipt_type' => 'Direct Receipt',
                    'received_by' => auth()->user()->name ?? 'James Mwangi',
                    'notes' => 'Point of Sale instant payment',
                ]);

                if ($validated['payment_method'] === 'Mpesa') {
                    $tillAccount = BankAccount::where('account_type', 'like', '%Till%')->orWhere('account_type', 'like', '%Paybill%')->first();
                    MpesaTransaction::create([
                        'mpesa_receipt_no' => $validated['reference_no'] ?: ('MP'.strtoupper(substr(uniqid(), 0, 8))),
                        'bank_account_id' => $tillAccount?->id,
                        'type' => 'Till',
                        'shortcode' => '543210',
                        'phone_number' => $customer->phone ?: '254700000000',
                        'customer_name' => $customer->name,
                        'amount' => $grandTotal,
                        'transaction_time' => now(),
                        'account_reference' => $invoiceNo,
                        'status' => 'Allocated',
                        'allocated_to_type' => 'SalesInvoice',
                        'allocated_to_id' => $invoice->id,
                    ]);
                    if ($tillAccount) {
                        $tillAccount->increment('current_balance', $grandTotal);
                    }
                } elseif ($validated['payment_method'] === 'Cash') {
                    $cashAccount = BankAccount::where('account_type', 'Petty Cash')->orWhere('account_type', 'Cash')->first();
                    if ($cashAccount) {
                        CashTransaction::create([
                            'txn_no' => 'CS-'.date('Y').'-'.str_pad((string) (CashTransaction::count() + 1), 5, '0', STR_PAD_LEFT),
                            'bank_account_id' => $cashAccount->id,
                            'txn_date' => now()->toDateString(),
                            'type' => 'INFLOW',
                            'category' => 'Sales Receipt',
                            'amount' => $grandTotal,
                            'payee_or_payer' => $customer->name,
                            'reference_no' => $invoiceNo,
                            'description' => "POS cash sale receipt {$invoiceNo}",
                            'approved_by' => 'Cashier',
                            'is_reconciled' => true,
                        ]);
                        $cashAccount->increment('current_balance', $grandTotal);
                    }
                }
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'POS Sale recorded successfully!',
                    'invoice_id' => $invoice->id,
                    'invoice_no' => $invoice->invoice_no,
                    'receipt_url' => route('pos.receipt', $invoice->id),
                    'grand_total' => $grandTotal,
                ]);
            }

            return redirect()->route('pos.receipt', $invoice->id)->with('success', 'Sale completed successfully!');
        });
    }

    public function receipt(SalesInvoice $invoice): View
    {
        $invoice->load(['customer', 'store', 'items.product', 'receipts']);

        return view('pos.receipt', compact('invoice'));
    }
}
