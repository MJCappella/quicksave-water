<?php

namespace App\Http\Controllers;

use App\Models\DebitNote;
use App\Models\GoodsReceivedNote;
use App\Models\GoodsReceivedNoteItem;
use App\Models\PaymentVoucher;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseQuote;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseRequisitionItem;
use App\Models\Store;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PayablesController extends Controller
{
    // 1. Vendors
    public function vendors(): View
    {
        $vendors = Vendor::withCount(['purchaseOrders', 'purchaseInvoices'])->orderBy('name')->get();
        $totalPayables = $vendors->sum('current_balance');

        return view('payables.vendors', compact('vendors', 'totalPayables'));
    }

    public function storeVendor(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:vendors,code',
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string|max:255',
            'tax_pin' => 'nullable|string|max:50',
            'payment_terms_days' => 'required|integer|min:0',
            'opening_balance' => 'required|numeric|min:0',
        ]);

        $validated['current_balance'] = $validated['opening_balance'];
        Vendor::create($validated);

        return redirect()->route('payables.vendors')->with('success', 'Vendor registered successfully!');
    }

    // 2. Purchase Requisitions
    public function requisitions(): View
    {
        $requisitions = PurchaseRequisition::with('items')->latest()->paginate(15);

        return view('payables.requisitions', compact('requisitions'));
    }

    public function storeRequisition(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'department' => 'required|string|max:100',
            'requested_by' => 'required|string|max:100',
            'requisition_date' => 'required|date',
            'required_date' => 'required|date',
            'priority' => 'required|string',
            'purpose' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.item_description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'required|string|max:50',
            'items.*.estimated_unit_cost' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            $reqNo = 'PR-'.date('Y').'-'.str_pad((string) (PurchaseRequisition::count() + 1), 4, '0', STR_PAD_LEFT);
            $totalEst = 0;
            foreach ($validated['items'] as $item) {
                $totalEst += $item['quantity'] * $item['estimated_unit_cost'];
            }

            $pr = PurchaseRequisition::create([
                'requisition_no' => $reqNo,
                'department' => $validated['department'],
                'requested_by' => $validated['requested_by'],
                'requisition_date' => $validated['requisition_date'],
                'required_date' => $validated['required_date'],
                'estimated_total' => $totalEst,
                'priority' => $validated['priority'],
                'status' => 'Pending',
                'purpose' => $validated['purpose'],
            ]);

            foreach ($validated['items'] as $item) {
                PurchaseRequisitionItem::create([
                    'purchase_requisition_id' => $pr->id,
                    'item_description' => $item['item_description'],
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'],
                    'estimated_unit_cost' => $item['estimated_unit_cost'],
                    'estimated_total' => $item['quantity'] * $item['estimated_unit_cost'],
                ]);
            }
        });

        return redirect()->route('payables.requisitions')->with('success', 'Purchase requisition submitted for approval!');
    }

    // 3. Purchase Quotes
    public function quotes(): View
    {
        $quotes = PurchaseQuote::with(['vendor', 'requisition'])->latest()->paginate(15);
        $vendors = Vendor::where('is_active', true)->get();
        $requisitions = PurchaseRequisition::all();

        return view('payables.quotes', compact('quotes', 'vendors', 'requisitions'));
    }

    public function storeQuote(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'purchase_requisition_id' => 'nullable|exists:purchase_requisitions,id',
            'quote_date' => 'required|date',
            'valid_until' => 'required|date',
            'total_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $quoteNo = 'PQ-'.date('Y').'-'.str_pad((string) (PurchaseQuote::count() + 1), 4, '0', STR_PAD_LEFT);
        $validated['quote_no'] = $quoteNo;
        $validated['status'] = 'Under Review';

        PurchaseQuote::create($validated);

        return redirect()->route('payables.quotes')->with('success', 'Vendor quotation recorded!');
    }

    // 4. Purchase Orders (PO)
    public function orders(): View
    {
        $orders = PurchaseOrder::with(['vendor', 'requisition', 'items'])->latest()->paginate(15);
        $vendors = Vendor::where('is_active', true)->get();
        $requisitions = PurchaseRequisition::all();

        return view('payables.orders', compact('orders', 'vendors', 'requisitions'));
    }

    public function storeOrder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'purchase_requisition_id' => 'nullable|exists:purchase_requisitions,id',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            $poNo = 'PO-'.date('Y').'-'.str_pad((string) (PurchaseOrder::count() + 1), 4, '0', STR_PAD_LEFT);
            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $subtotal += $item['quantity'] * $item['unit_price'];
            }
            $vat = round($subtotal * 0.16, 2);
            $grandTotal = $subtotal + $vat;

            $po = PurchaseOrder::create([
                'po_no' => $poNo,
                'vendor_id' => $validated['vendor_id'],
                'purchase_requisition_id' => $validated['purchase_requisition_id'] ?? null,
                'order_date' => $validated['order_date'],
                'expected_delivery_date' => $validated['expected_delivery_date'] ?? null,
                'subtotal' => $subtotal,
                'tax_amount' => $vat,
                'grand_total' => $grandTotal,
                'status' => 'Sent',
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $total = $item['quantity'] * $item['unit_price'];
                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'item_description' => $item['item_description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_amount' => round($total * 0.16, 2),
                    'total' => $total,
                ]);
            }
        });

        return redirect()->route('payables.orders')->with('success', 'Purchase order created and sent to supplier!');
    }

    // 5. Goods Received Note (G.R.N)
    public function grns(): View
    {
        $grns = GoodsReceivedNote::with(['vendor', 'purchaseOrder', 'store', 'items'])->latest()->paginate(15);
        $purchaseOrders = PurchaseOrder::all();
        $vendors = Vendor::where('is_active', true)->get();
        $stores = Store::where('is_active', true)->get();

        return view('payables.grns', compact('grns', 'purchaseOrders', 'vendors', 'stores'));
    }

    public function storeGrn(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'vendor_id' => 'required|exists:vendors,id',
            'store_id' => 'required|exists:stores,id',
            'received_date' => 'required|date',
            'delivery_note_ref' => 'nullable|string|max:100',
            'received_by' => 'required|string|max:100',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_description' => 'required|string|max:255',
            'items.*.quantity_ordered' => 'required|numeric|min:0',
            'items.*.quantity_received' => 'required|numeric|min:0',
            'items.*.quantity_accepted' => 'required|numeric|min:0',
            'items.*.quantity_rejected' => 'nullable|numeric|min:0',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            $grnNo = 'GRN-'.date('Y').'-'.str_pad((string) (GoodsReceivedNote::count() + 1), 4, '0', STR_PAD_LEFT);

            $grn = GoodsReceivedNote::create([
                'grn_no' => $grnNo,
                'purchase_order_id' => $validated['purchase_order_id'] ?? null,
                'vendor_id' => $validated['vendor_id'],
                'store_id' => $validated['store_id'],
                'received_date' => $validated['received_date'],
                'delivery_note_ref' => $validated['delivery_note_ref'] ?? null,
                'received_by' => $validated['received_by'],
                'status' => 'Verified',
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                GoodsReceivedNoteItem::create([
                    'goods_received_note_id' => $grn->id,
                    'item_description' => $item['item_description'],
                    'quantity_ordered' => $item['quantity_ordered'],
                    'quantity_received' => $item['quantity_received'],
                    'quantity_accepted' => $item['quantity_accepted'],
                    'quantity_rejected' => $item['quantity_rejected'] ?? 0,
                    'unit_cost' => $item['unit_cost'],
                ]);
            }
        });

        return redirect()->route('payables.grns')->with('success', 'Goods Received Note (G.R.N) certified and received in store!');
    }

    // 6. Purchase Invoices (Supplier Bills)
    public function invoices(): View
    {
        $invoices = PurchaseInvoice::with(['vendor', 'grn', 'items', 'paymentVouchers'])->latest()->paginate(15);
        $vendors = Vendor::where('is_active', true)->get();
        $grns = GoodsReceivedNote::all();

        return view('payables.invoices', compact('invoices', 'vendors', 'grns'));
    }

    public function storeInvoice(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'vendor_invoice_no' => 'required|string|max:100',
            'goods_received_note_id' => 'nullable|exists:goods_received_notes,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            $invNo = 'PINV-'.date('Y').'-'.str_pad((string) (PurchaseInvoice::count() + 1), 4, '0', STR_PAD_LEFT);
            $vendor = Vendor::findOrFail($validated['vendor_id']);

            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $subtotal += $item['quantity'] * $item['unit_price'];
            }
            $vat = round($subtotal * 0.16, 2);
            $grandTotal = $subtotal + $vat;

            $inv = PurchaseInvoice::create([
                'invoice_no' => $invNo,
                'vendor_invoice_no' => $validated['vendor_invoice_no'],
                'vendor_id' => $vendor->id,
                'goods_received_note_id' => $validated['goods_received_note_id'] ?? null,
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'subtotal' => $subtotal,
                'tax_amount' => $vat,
                'grand_total' => $grandTotal,
                'paid_amount' => 0,
                'balance_due' => $grandTotal,
                'payment_status' => 'Unpaid',
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $lineTotal = $item['quantity'] * $item['unit_price'];
                PurchaseInvoiceItem::create([
                    'purchase_invoice_id' => $inv->id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_amount' => round($lineTotal * 0.16, 2),
                    'total' => $lineTotal,
                ]);
            }

            $vendor->increment('current_balance', $grandTotal);
        });

        return redirect()->route('payables.invoices')->with('success', 'Purchase invoice booked and vendor balance credited!');
    }

    // 7. Debit Notes
    public function debitNotes(): View
    {
        $debitNotes = DebitNote::with(['vendor', 'invoice'])->latest()->paginate(15);
        $vendors = Vendor::where('is_active', true)->get();
        $invoices = PurchaseInvoice::all();

        return view('payables.debit-notes', compact('debitNotes', 'vendors', 'invoices'));
    }

    public function storeDebitNote(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'purchase_invoice_id' => 'nullable|exists:purchase_invoices,id',
            'debit_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $dnNo = 'DN-AP-'.date('Y').'-'.str_pad((string) (DebitNote::count() + 1), 4, '0', STR_PAD_LEFT);
            $vendor = Vendor::findOrFail($validated['vendor_id']);
            $amount = (float) $validated['amount'];

            DebitNote::create([
                'debit_note_no' => $dnNo,
                'vendor_id' => $vendor->id,
                'purchase_invoice_id' => $validated['purchase_invoice_id'] ?? null,
                'debit_date' => $validated['debit_date'],
                'amount' => $amount,
                'reason' => $validated['reason'],
                'status' => 'Approved',
                'notes' => $validated['notes'] ?? null,
            ]);

            $vendor->decrement('current_balance', $amount);

            if (! empty($validated['purchase_invoice_id'])) {
                $invoice = PurchaseInvoice::find($validated['purchase_invoice_id']);
                if ($invoice) {
                    $invoice->decrement('balance_due', min($invoice->balance_due, $amount));
                }
            }
        });

        return redirect()->route('payables.debit-notes')->with('success', 'Debit note issued to vendor!');
    }

    // 8. Payment Vouchers
    public function vouchers(): View
    {
        $vouchers = PaymentVoucher::with(['vendor', 'invoice'])->latest()->paginate(15);
        $vendors = Vendor::where('is_active', true)->get();
        $invoices = PurchaseInvoice::where('balance_due', '>', 0)->get();

        return view('payables.vouchers', compact('vouchers', 'vendors', 'invoices'));
    }

    public function storeVoucher(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'purchase_invoice_id' => 'nullable|exists:purchase_invoices,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|in:Bank Transfer,Cheque,Mpesa,Cash',
            'reference_no' => 'nullable|string|max:100',
            'paid_from' => 'required|string|max:150',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $pvNo = 'PV-'.date('Y').'-'.str_pad((string) (PaymentVoucher::count() + 1), 4, '0', STR_PAD_LEFT);
            $vendor = Vendor::findOrFail($validated['vendor_id']);
            $amount = (float) $validated['amount'];

            PaymentVoucher::create([
                'voucher_no' => $pvNo,
                'vendor_id' => $vendor->id,
                'purchase_invoice_id' => $validated['purchase_invoice_id'] ?? null,
                'payment_date' => $validated['payment_date'],
                'amount' => $amount,
                'payment_method' => $validated['payment_method'],
                'reference_no' => $validated['reference_no'] ?? null,
                'paid_from' => $validated['paid_from'],
                'approved_by' => auth()->user()->name ?? 'Finance Director',
                'prepared_by' => 'Accounts Payable Accountant',
                'notes' => $validated['notes'] ?? null,
            ]);

            // Deduct vendor balance
            $vendor->decrement('current_balance', $amount);

            if (! empty($validated['purchase_invoice_id'])) {
                $invoice = PurchaseInvoice::find($validated['purchase_invoice_id']);
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

        return redirect()->route('payables.vouchers')->with('success', 'Payment voucher authorized and vendor paid!');
    }

    // 9. AP Aging Report
    public function agingReport(): View
    {
        $vendors = Vendor::where('is_active', true)->with('purchaseInvoices')->get();

        $agingData = $vendors->map(function ($vend) {
            $unpaidInvoices = $vend->purchaseInvoices->where('balance_due', '>', 0);
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
                'vendor' => $vend,
                'days_30' => $days30,
                'days_60' => $days60,
                'days_90' => $days90,
                'days_90_plus' => $days90Plus,
                'total_due' => $vend->current_balance,
            ];
        });

        $totalAP = $vendors->sum('current_balance');

        return view('payables.aging-report', compact('agingData', 'totalAP'));
    }

    // 10. Purchase Reports
    public function reports(Request $request): View
    {
        $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', now()->toDateString());

        $invoices = PurchaseInvoice::with(['vendor', 'items'])
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->latest()
            ->get();

        $totalPurchases = $invoices->sum('grand_total');
        $totalPaid = $invoices->sum('paid_amount');
        $totalOwed = $invoices->sum('balance_due');

        return view('payables.reports', compact('invoices', 'totalPurchases', 'totalPaid', 'totalOwed', 'startDate', 'endDate'));
    }
}
