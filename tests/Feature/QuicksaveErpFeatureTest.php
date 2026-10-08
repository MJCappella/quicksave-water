<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Store;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuicksaveErpFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected string $seeder = DatabaseSeeder::class;

    public function test_dashboard_screen_can_be_rendered(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('QUICKSAVE AGENCIES LTD');
        $response->assertSee('Dashboard');
    }

    public function test_pos_screen_can_be_rendered_and_checkout_works(): void
    {
        $response = $this->get(route('pos.index'));
        $response->assertStatus(200);
        $response->assertSee('Point of Sale');

        $store = Store::first();
        $product = Product::first();
        $customer = Customer::first();

        $postData = [
            'store_id' => $store->id,
            'customer_id' => $customer->id,
            'payment_method' => 'Cash',
            'amount_paid' => 1500,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'unit_price' => $product->retail_price,
                ],
            ],
        ];

        $checkoutResponse = $this->postJson(route('pos.checkout'), $postData);
        $checkoutResponse->assertStatus(200);
        $checkoutResponse->assertJsonStructure([
            'success',
            'invoice_id',
            'receipt_url',
            'invoice_no',
            'grand_total',
        ]);
    }

    public function test_production_module_screens_can_be_rendered(): void
    {
        $this->get(route('production.index'))->assertStatus(200)->assertSee('Batches');
        $this->get(route('production.materials'))->assertStatus(200)->assertSee('Material Inventory');
        $this->get(route('production.utilization'))->assertStatus(200)->assertSee('Utilization');
    }

    public function test_inventory_and_asset_screens_can_be_rendered(): void
    {
        $this->get(route('inventory.index'))->assertStatus(200)->assertSee('Finished Products Inventory');
        $this->get(route('inventory.transfers'))->assertStatus(200)->assertSee('Store Transfers');
        $this->get(route('inventory.adjustments'))->assertStatus(200)->assertSee('Stock Adjustments');
        $this->get(route('inventory.movements'))->assertStatus(200)->assertSee('Stock Movement');
        $this->get(route('assets.index'))->assertStatus(200)->assertSee('Fixed Asset');
    }

    public function test_sales_and_receivables_screens_can_be_rendered(): void
    {
        $this->get(route('sales.customers'))->assertStatus(200)->assertSee('Customer');
        $this->get(route('sales.quotations'))->assertStatus(200)->assertSee('Quotation');
        $this->get(route('sales.orders'))->assertStatus(200)->assertSee('Sales Order');
        $this->get(route('sales.delivery-notes'))->assertStatus(200)->assertSee('Delivery Note');
        $this->get(route('sales.invoices'))->assertStatus(200)->assertSee('Sales Invoice');
        $this->get(route('sales.credit-notes'))->assertStatus(200)->assertSee('Credit Note');
        $this->get(route('sales.receipts'))->assertStatus(200)->assertSee('Receipt');
        $this->get(route('sales.aging'))->assertStatus(200)->assertSee('Aging');
        $this->get(route('sales.reports'))->assertStatus(200)->assertSee('Sales');
    }

    public function test_payables_module_screens_can_be_rendered(): void
    {
        $this->get(route('payables.vendors'))->assertStatus(200)->assertSee('Vendor');
        $this->get(route('payables.requisitions'))->assertStatus(200)->assertSee('Requisition');
        $this->get(route('payables.quotes'))->assertStatus(200)->assertSee('Quote');
        $this->get(route('payables.orders'))->assertStatus(200)->assertSee('Purchase Order');
        $this->get(route('payables.grns'))->assertStatus(200)->assertSee('Goods Received');
        $this->get(route('payables.invoices'))->assertStatus(200)->assertSee('Purchase Invoice');
        $this->get(route('payables.debit-notes'))->assertStatus(200)->assertSee('Debit Note');
        $this->get(route('payables.vouchers'))->assertStatus(200)->assertSee('Voucher');
        $this->get(route('payables.aging'))->assertStatus(200)->assertSee('Aging');
        $this->get(route('payables.reports'))->assertStatus(200)->assertSee('Procurement');
    }

    public function test_banking_and_funds_screens_can_be_rendered(): void
    {
        $this->get(route('banking.petty-cash'))->assertStatus(200)->assertSee('Petty Cash');
        $this->get(route('banking.main-cash'))->assertStatus(200)->assertSee('Cashbook');
        $this->get(route('banking.mpesa'))->assertStatus(200)->assertSee('M-Pesa');
        $this->get(route('banking.reconciliation'))->assertStatus(200)->assertSee('Bank Reconciliation');
    }

    public function test_financial_reports_screens_can_be_rendered(): void
    {
        $this->get(route('reports.accounts-ledger'))->assertStatus(200)->assertSee('Ledger');
        $this->get(route('reports.general-ledger'))->assertStatus(200)->assertSee('General Ledger');
        $this->get(route('reports.trial-balance'))->assertStatus(200)->assertSee('Trial Balance');
        $this->get(route('reports.income-statement'))->assertStatus(200)->assertSee('INCOME');
        $this->get(route('reports.balance-sheet'))->assertStatus(200)->assertSee('FINANCIAL POSITION');
    }
}
