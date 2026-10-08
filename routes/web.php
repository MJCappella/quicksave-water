<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\BankingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PayablesController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalesController;
use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index']);

// Point of Sale (POS)
Route::prefix('pos')->name('pos.')->group(function () {
    Route::get('/', [PosController::class, 'index'])->name('index');
    Route::post('/', [PosController::class, 'store'])->name('store');
    Route::post('/checkout', [PosController::class, 'store'])->name('checkout');
    Route::get('/receipt/{invoice}', [PosController::class, 'receipt'])->name('receipt');
});

// Production & Material Inventory
Route::prefix('production')->name('production.')->group(function () {
    Route::get('/', [ProductionController::class, 'index'])->name('index');
    Route::post('/batches', [ProductionController::class, 'storeBatch'])->name('batches.store');
    Route::get('/materials', [ProductionController::class, 'materials'])->name('materials');
    Route::post('/materials', [ProductionController::class, 'storeMaterial'])->name('materials.store');
    Route::post('/purchases', [ProductionController::class, 'storePurchase'])->name('purchases.store');
    Route::get('/utilization', [ProductionController::class, 'utilization'])->name('utilization');
});

// Inventory & Asset Tracking
Route::prefix('inventory')->name('inventory.')->group(function () {
    Route::get('/', [InventoryController::class, 'index'])->name('index');
    Route::get('/transfers', [InventoryController::class, 'transfers'])->name('transfers');
    Route::post('/transfers', [InventoryController::class, 'storeTransfer'])->name('transfers.store');
    Route::get('/adjustments', [InventoryController::class, 'adjustments'])->name('adjustments');
    Route::post('/adjustments', [InventoryController::class, 'storeAdjustment'])->name('adjustments.store');
    Route::get('/movements', [InventoryController::class, 'movements'])->name('movements');
});

// Fixed Asset Register
Route::prefix('assets')->name('assets.')->group(function () {
    Route::get('/', [AssetController::class, 'index'])->name('index');
    Route::post('/', [AssetController::class, 'store'])->name('store');
});

// Sales & Receivables (AR)
Route::prefix('sales')->name('sales.')->group(function () {
    Route::get('/customers', [SalesController::class, 'customers'])->name('customers');
    Route::post('/customers', [SalesController::class, 'storeCustomer'])->name('customers.store');
    Route::get('/quotations', [SalesController::class, 'quotations'])->name('quotations');
    Route::post('/quotations', [SalesController::class, 'storeQuotation'])->name('quotations.store');
    Route::get('/orders', [SalesController::class, 'orders'])->name('orders');
    Route::post('/orders', [SalesController::class, 'storeOrder'])->name('orders.store');
    Route::get('/delivery-notes', [SalesController::class, 'deliveryNotes'])->name('delivery-notes');
    Route::post('/delivery-notes', [SalesController::class, 'storeDeliveryNote'])->name('delivery-notes.store');
    Route::get('/invoices', [SalesController::class, 'invoices'])->name('invoices');
    Route::post('/invoices', [SalesController::class, 'storeInvoice'])->name('invoices.store');
    Route::get('/credit-notes', [SalesController::class, 'creditNotes'])->name('credit-notes');
    Route::post('/credit-notes', [SalesController::class, 'storeCreditNote'])->name('credit-notes.store');
    Route::get('/receipts', [SalesController::class, 'receipts'])->name('receipts');
    Route::post('/receipts', [SalesController::class, 'storeReceipt'])->name('receipts.store');
    Route::get('/aging-report', [SalesController::class, 'agingReport'])->name('aging-report');
    Route::get('/aging', [SalesController::class, 'agingReport'])->name('aging');
    Route::get('/reports', [SalesController::class, 'reports'])->name('reports');
});

// Payables (AP)
Route::prefix('payables')->name('payables.')->group(function () {
    Route::get('/vendors', [PayablesController::class, 'vendors'])->name('vendors');
    Route::post('/vendors', [PayablesController::class, 'storeVendor'])->name('vendors.store');
    Route::get('/requisitions', [PayablesController::class, 'requisitions'])->name('requisitions');
    Route::post('/requisitions', [PayablesController::class, 'storeRequisition'])->name('requisitions.store');
    Route::get('/quotes', [PayablesController::class, 'quotes'])->name('quotes');
    Route::post('/quotes', [PayablesController::class, 'storeQuote'])->name('quotes.store');
    Route::get('/orders', [PayablesController::class, 'orders'])->name('orders');
    Route::post('/orders', [PayablesController::class, 'storeOrder'])->name('orders.store');
    Route::get('/grns', [PayablesController::class, 'grns'])->name('grns');
    Route::post('/grns', [PayablesController::class, 'storeGrn'])->name('grns.store');
    Route::get('/invoices', [PayablesController::class, 'invoices'])->name('invoices');
    Route::post('/invoices', [PayablesController::class, 'storeInvoice'])->name('invoices.store');
    Route::get('/debit-notes', [PayablesController::class, 'debitNotes'])->name('debit-notes');
    Route::post('/debit-notes', [PayablesController::class, 'storeDebitNote'])->name('debit-notes.store');
    Route::get('/vouchers', [PayablesController::class, 'vouchers'])->name('vouchers');
    Route::post('/vouchers', [PayablesController::class, 'storeVoucher'])->name('vouchers.store');
    Route::get('/aging-report', [PayablesController::class, 'agingReport'])->name('aging-report');
    Route::get('/aging', [PayablesController::class, 'agingReport'])->name('aging');
    Route::get('/reports', [PayablesController::class, 'reports'])->name('reports');
});

// Funds / Bank Management
Route::prefix('banking')->name('banking.')->group(function () {
    Route::get('/petty-cash', [BankingController::class, 'pettyCash'])->name('petty-cash');
    Route::post('/petty-cash', [BankingController::class, 'storePettyCash'])->name('petty-cash.store');
    Route::get('/main-cash', [BankingController::class, 'mainCash'])->name('main-cash');
    Route::post('/main-cash', [BankingController::class, 'storeMainCash'])->name('main-cash.store');
    Route::get('/mpesa', [BankingController::class, 'mpesa'])->name('mpesa');
    Route::post('/mpesa/{transaction}/allocate', [BankingController::class, 'allocateMpesa'])->name('mpesa.allocate');
    Route::get('/reconciliation', [BankingController::class, 'reconciliation'])->name('reconciliation');
    Route::post('/reconciliation', [BankingController::class, 'storeReconciliation'])->name('reconciliation.store');
});

// Financial Reports & General Ledger
Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/general-ledger', [ReportController::class, 'generalLedger'])->name('general-ledger');
    Route::get('/accounts-ledger', [ReportController::class, 'accountsLedger'])->name('accounts-ledger');
    Route::get('/trial-balance', [ReportController::class, 'trialBalance'])->name('trial-balance');
    Route::get('/income-statement', [ReportController::class, 'incomeStatement'])->name('income-statement');
    Route::get('/balance-sheet', [ReportController::class, 'balanceSheet'])->name('balance-sheet');
});
