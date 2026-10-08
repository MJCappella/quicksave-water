<?php

namespace App\Http\Controllers;

use App\Models\CreditNote;
use App\Models\Customer;
use App\Models\CustomerReceipt;
use App\Models\DeliveryNote;
use App\Models\DeliveryNoteItem;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\StockMovement;
use App\Models\Store;
use App\Models\StoreInventory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SalesController extends Controller
{
    // 1. Customers
    public function customers(): View
    {
        $customers = Customer::withCount(['invoices', 'receipts'])->orderBy('name')->get();
        $totalReceivables = $customers->sum('current_balance');

        return view('sales.customers', compact('customers', 'totalReceivables'));
    }

    public function storeCustomer(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:customers,code',
            'name' => 'required|string|max:255',
            'customer_type' => 'required|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string|max:255',
            'tax_pin' => 'nullable|string|max:50',
            'credit_limit' => 'required|numeric|min:0',
            'payment_terms_days' => 'required|integer|min:0',
            'opening_balance' => 'required|numeric|min:0',
        ]);

        $validated['current_balance'] = $validated['opening_balance'];
        Customer::create($validated);

        return redirect()->route('sales.customers')->with('success', 'Customer added successfully!');
    }

    // 2. Quotations
    public function quotations(): View
    {
        $quotations = Quotation::with(['customer', 'items.product'])->latest()->paginate(15);
        $customers = Customer::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();

        return view('sales.quotations', compact('quotations', 'customers', 'products'));
    }

    public function storeQuotation(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'quote_date' => 'required|date',
            'valid_until' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            $quoteNo = 'QT-'.date('Y').'-'.str_pad((string) (Quotation::count() + 1), 4, '0', STR_PAD_LEFT);
            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $subtotal += $item['quantity'] * $item['unit_price'];
            }
            $vat = round($subtotal * 0.16, 2);
            $grandTotal = $subtotal + $vat;

            $quote = Quotation::create([
                'quote_no' => $quoteNo,
                'customer_id' => $validated['customer_id'],
                'quote_date' => $validated['quote_date'],
                'valid_until' => $validated['valid_until'],
                'subtotal' => $subtotal,
                'tax_amount' => $vat,
                'discount_amount' => 0,
                'grand_total' => $grandTotal,
                'status' => 'Pending',
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->user()->name ?? 'Sales Rep',
            ]);

            foreach ($validated['items'] as $item) {
                $total = $item['quantity'] * $item['unit_price'];
                QuotationItem::create([
                    'quotation_id' => $quote->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount' => 0,
                    'tax_amount' => round($total * 0.16, 2),
                    'total' => $total,
                ]);
            }
        });

        return redirect()->route('sales.quotations')->with('success', 'Quotation generated successfully!');
    }

    // 3. Sales Orders
    public function orders(): View
    {
        $orders = SalesOrder::with(['customer', 'quotation', 'items.product'])->latest()->paginate(15);
        $customers = Customer::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();

        return view('sales.orders', compact('orders', 'customers', 'products'));
    }

    public function storeOrder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'order_date' => 'required|date',
            'delivery_due_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            $orderNo = 'SO-'.date('Y').'-'.str_pad((string) (SalesOrder::count() + 1), 4, '0', STR_PAD_LEFT);
            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $subtotal += $item['quantity'] * $item['unit_price'];
            }
            $vat = round($subtotal * 0.16, 2);
            $grandTotal = $subtotal + $vat;

            $so = SalesOrder::create([
                'order_no' => $orderNo,
                'customer_id' => $validated['customer_id'],
                'order_date' => $validated['order_date'],
                'delivery_due_date' => $validated['delivery_due_date'] ?? null,
                'subtotal' => $subtotal,
                'tax_amount' => $vat,
                'grand_total' => $grandTotal,
                'status' => 'Confirmed',
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $total = $item['quantity'] * $item['unit_price'];
                SalesOrderItem::create([
                    'sales_order_id' => $so->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_amount' => round($total * 0.16, 2),
                    'total' => $total,
                ]);
            }
        });

        return redirect()->route('sales.orders')->with('success', 'Sales order created successfully!');
    }

    // 4. Delivery Notes
    public function deliveryNotes(): View
    {
        $deliveryNotes = DeliveryNote::with(['customer', 'store', 'salesOrder', 'items.product'])->latest()->paginate(15);
        $customers = Customer::where('is_active', true)->get();
        $stores = Store::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();

        return view('sales.delivery-notes', compact('deliveryNotes', 'customers', 'stores', 'products'));
    }

    public function storeDeliveryNote(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'store_id' => 'required|exists:stores,id',
            'delivery_date' => 'required|date',
            'driver_name' => 'nullable|string|max:255',
            'vehicle_reg' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_ordered' => 'required|integer|min:1',
            'items.*.quantity_delivered' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($validated) {
            $dnNo = 'DN-'.date('Y').'-'.str_pad((string) (DeliveryNote::count() + 1), 4, '0', STR_PAD_LEFT);

            $dn = DeliveryNote::create([
                'delivery_no' => $dnNo,
                'customer_id' => $validated['customer_id'],
                'store_id' => $validated['store_id'],
                'delivery_date' => $validated['delivery_date'],
                'driver_name' => $validated['driver_name'] ?? null,
                'vehicle_reg' => $validated['vehicle_reg'] ?? null,
                'status' => 'Dispatched',
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                DeliveryNoteItem::create([
                    'delivery_note_id' => $dn->id,
                    'product_id' => $item['product_id'],
                    'quantity_ordered' => $item['quantity_ordered'],
                    'quantity_delivered' => $item['quantity_delivered'],
                ]);
            }
        });

        return redirect()->route('sales.delivery-notes')->with('success', 'Delivery note dispatched successfully!');
    }

    // 5. Sales Invoices
    public function invoices(): View
    {
        $invoices = SalesInvoice::with(['customer', 'store', 'items.product', 'receipts'])->latest()->paginate(15);
        $customers = Customer::where('is_active', true)->get();
        $stores = Store::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();

        return view('sales.invoices', compact('invoices', 'customers', 'stores', 'products'));
    }

    public function storeInvoice(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'store_id' => 'required|exists:stores,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            $invNo = 'INV-'.date('Y').'-'.str_pad((string) (SalesInvoice::where('is_pos', false)->count() + 1), 4, '0', STR_PAD_LEFT);
            $customer = Customer::findOrFail($validated['customer_id']);
            $store = Store::findOrFail($validated['store_id']);

            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $subtotal += $item['quantity'] * $item['unit_price'];
            }
            $vat = round($subtotal * 0.16, 2);
            $grandTotal = $subtotal + $vat;

            $inv = SalesInvoice::create([
                'invoice_no' => $invNo,
                'customer_id' => $customer->id,
                'store_id' => $store->id,
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'subtotal' => $subtotal,
                'tax_amount' => $vat,
                'discount_amount' => 0,
                'grand_total' => $grandTotal,
                'paid_amount' => 0,
                'balance_due' => $grandTotal,
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'Unpaid',
                'is_pos' => false,
                'cashier_name' => auth()->user()->name ?? 'Billing Officer',
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $qty = (int) $item['quantity'];
                $price = (float) $item['unit_price'];
                $lineTotal = $qty * $price;

                SalesInvoiceItem::create([
                    'sales_invoice_id' => $inv->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'discount' => 0,
                    'tax_amount' => round($lineTotal * 0.16, 2),
                    'total' => $lineTotal,
                ]);

                // Deduct stock from store
                $inventory = StoreInventory::firstOrCreate(
                    ['store_id' => $store->id, 'product_id' => $product->id],
                    ['quantity' => 0]
                );
                $balBefore = $inventory->quantity;
                $inventory->decrement('quantity', $qty);

                StockMovement::create([
                    'product_id' => $product->id,
                    'store_id' => $store->id,
                    'type' => 'SALE',
                    'reference_type' => 'SaleInvoice',
                    'reference_no' => $invNo,
                    'quantity_change' => -$qty,
                    'balance_before' => $balBefore,
                    'balance_after' => $inventory->fresh()->quantity,
                    'user_name' => auth()->user()->name ?? 'Sales Admin',
                    'notes' => "Invoice issued to {$customer->name}",
                ]);
            }

            $customer->increment('current_balance', $grandTotal);
        });

        return redirect()->route('sales.invoices')->with('success', 'Sales invoice generated and AR account debited!');
    }

    // 6. Credit Notes
    public function creditNotes(): View
    {
        $creditNotes = CreditNote::with(['customer', 'invoice', 'items.product'])->latest()->paginate(15);
        $customers = Customer::where('is_active', true)->get();
        $invoices = SalesInvoice::latest()->take(20)->get();
        $products = Product::where('is_active', true)->get();

        return view('sales.credit-notes', compact('creditNotes', 'customers', 'invoices', 'products'));
    }

    public function storeCreditNote(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'sales_invoice_id' => 'nullable|exists:sales_invoices,id',
            'credit_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $cnNo = 'CN-'.date('Y').'-'.str_pad((string) (CreditNote::count() + 1), 4, '0', STR_PAD_LEFT);
            $customer = Customer::findOrFail($validated['customer_id']);
            $amount = (float) $validated['amount'];

            CreditNote::create([
                'credit_note_no' => $cnNo,
                'customer_id' => $customer->id,
                'sales_invoice_id' => $validated['sales_invoice_id'] ?? null,
                'credit_date' => $validated['credit_date'],
                'amount' => $amount,
                'reason' => $validated['reason'],
                'status' => 'Applied',
                'notes' => $validated['notes'] ?? null,
            ]);

            // Deduct from customer balance
            $customer->decrement('current_balance', $amount);

            if (! empty($validated['sales_invoice_id'])) {
                $invoice = SalesInvoice::find($validated['sales_invoice_id']);
                if ($invoice) {
                    $invoice->decrement('balance_due', min($invoice->balance_due, $amount));
                }
            }
        });

        return redirect()->route('sales.credit-notes')->with('success', 'Credit note posted and customer balance credited!');
    }

    // 7. Customer Receipts & Direct Receipts
    public function receipts(): View
    {
        $receipts = CustomerReceipt::with(['customer', 'invoice'])->latest()->paginate(15);
        $customers = Customer::where('is_active', true)->get();
        $invoices = SalesInvoice::where('balance_due', '>', 0)->get();

        return view('sales.receipts', compact('receipts', 'customers', 'invoices'));
    }

    public function storeReceipt(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'sales_invoice_id' => 'nullable|exists:sales_invoices,id',
            'receipt_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|in:Cash,Mpesa,Bank Transfer,Cheque',
            'reference_no' => 'nullable|string|max:100',
            'receipt_type' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $rcpNo = 'RCP-'.date('Y').'-'.str_pad((string) (CustomerReceipt::count() + 1), 5, '0', STR_PAD_LEFT);
            $customer = Customer::findOrFail($validated['customer_id']);
            $amount = (float) $validated['amount'];

            CustomerReceipt::create([
                'receipt_no' => $rcpNo,
                'customer_id' => $customer->id,
                'sales_invoice_id' => $validated['sales_invoice_id'] ?? null,
                'receipt_date' => $validated['receipt_date'],
                'amount' => $amount,
                'payment_method' => $validated['payment_method'],
                'reference_no' => $validated['reference_no'] ?? null,
                'receipt_type' => $validated['receipt_type'],
                'received_by' => auth()->user()->name ?? 'Finance Officer',
                'notes' => $validated['notes'] ?? null,
            ]);

            // Deduct customer outstanding balance
            $customer->decrement('current_balance', $amount);

            // Update invoice if linked
            if (! empty($validated['sales_invoice_id'])) {
                $invoice = SalesInvoice::find($validated['sales_invoice_id']);
                if ($invoice) {
                    $invoice->increment('paid_amount', $amount);
                    $newBal = max(0, $invoice->grand_total - $invoice->paid_amount);
                    $invoice->update([
                        'balance_due' => $newBal,
                        'payment_status' => $newBal <= 0 ? 'Paid' : 'Partial',
                    ]);
                }
            }
        });

        return redirect()->route('sales.receipts')->with('success', 'Customer payment received and receipt recorded!');
    }

    // 8. AR Aging Report
    public function agingReport(): View
    {
        $customers = Customer::where('is_active', true)->with('invoices')->get();

        $agingData = $customers->map(function ($cust) {
            $unpaidInvoices = $cust->invoices->where('balance_due', '>', 0);
            $current = 0;
            $days30 = 0;
            $days60 = 0;
            $days90 = 0;
            $days90Plus = 0;

            foreach ($unpaidInvoices as $inv) {
                $age = now()->diffInDays($inv->invoice_date);
                if ($age <= 30) {
                    $days30 += $inv->balance_due;
                } elseif ($age <= 60) {
                    $days60 += $inv->balance_due;
                } elseif ($age <= 90) {
                    $days90 += $inv->balance_due;
                } else {
                    $days90Plus += $inv->balance_due;
                }
            }

            return [
                'customer' => $cust,
                'current' => $current,
                'days_30' => $days30,
                'days_60' => $days60,
                'days_90' => $days90,
                'days_90_plus' => $days90Plus,
                'total_due' => $cust->current_balance,
            ];
        });

        $totalAR = $customers->sum('current_balance');

        return view('sales.aging-report', compact('agingData', 'totalAR'));
    }

    // 9. Sales Reports
    public function reports(Request $request): View
    {
        $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', now()->toDateString());

        $invoices = SalesInvoice::with(['customer', 'store', 'items.product'])
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->latest()
            ->get();

        $totalSales = $invoices->sum('grand_total');
        $totalPaid = $invoices->sum('paid_amount');
        $totalPending = $invoices->sum('balance_due');

        return view('sales.reports', compact('invoices', 'totalSales', 'totalPaid', 'totalPending', 'startDate', 'endDate'));
    }
}
