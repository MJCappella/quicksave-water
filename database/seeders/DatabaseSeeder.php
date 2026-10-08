<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\BankReconciliation;
use App\Models\BankStatementLine;
use App\Models\CashTransaction;
use App\Models\ChartOfAccount;
use App\Models\Customer;
use App\Models\CustomerReceipt;
use App\Models\DeliveryNote;
use App\Models\DeliveryNoteItem;
use App\Models\FixedAsset;
use App\Models\GoodsReceivedNote;
use App\Models\GoodsReceivedNoteItem;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\MaterialPurchase;
use App\Models\MaterialPurchaseItem;
use App\Models\MpesaTransaction;
use App\Models\Product;
use App\Models\ProductionBatch;
use App\Models\ProductionMaterialUsage;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseRequisitionItem;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\RawMaterial;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\StockMovement;
use App\Models\Store;
use App\Models\StoreInventory;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 0. Default Admin User
        $user = User::firstOrCreate(
            ['email' => 'admin@quicksavewater.co.ke'],
            [
                'name' => 'James Mwangi',
                'password' => Hash::make('password123'),
            ]
        );

        // 1. Stores / Warehouses & Delivery Units
        $mainPlant = Store::firstOrCreate(['code' => 'STR-NRB-01'], [
            'name' => 'Superior Center Plant & Warehouse',
            'type' => 'Factory Warehouse',
            'location' => 'Industrial Area, Commercial St., Nairobi',
            'manager_name' => 'James Mwangi (Branch Mgr)',
            'phone' => '+254 722 100 200',
            'is_active' => true,
        ]);

        $depotWest = Store::firstOrCreate(['code' => 'STR-WST-02'], [
            'name' => 'Westlands Dispatch Depot',
            'type' => 'Regional Depot',
            'location' => 'Waiyaki Way, Westlands, Nairobi',
            'manager_name' => 'David Omondi',
            'phone' => '+254 733 456 789',
            'is_active' => true,
        ]);

        $vanMobile1 = Store::firstOrCreate(['code' => 'VAN-KDA-421'], [
            'name' => 'Route Delivery Truck - KDA 421B',
            'type' => 'Mobile Van Store',
            'location' => 'Nairobi CBD & Upper Hill Circuit',
            'manager_name' => 'Peter Karanja',
            'phone' => '+254 711 987 654',
            'is_active' => true,
        ]);

        // 2. Finished Water Products
        $p500 = Product::firstOrCreate(['sku' => 'QSW-500ML-24'], [
            'name' => 'Quicksave Purified Water 500ml (Pack of 24)',
            'category' => 'Bottled Water',
            'unit_measure' => 'Pack of 24',
            'wholesale_price' => 450.00,
            'retail_price' => 580.00,
            'cost_price' => 320.00,
            'reorder_level' => 50,
            'is_active' => true,
        ]);

        $p1000 = Product::firstOrCreate(['sku' => 'QSW-1L-12'], [
            'name' => 'Quicksave Purified Water 1 Litre (Pack of 12)',
            'category' => 'Bottled Water',
            'unit_measure' => 'Pack of 12',
            'wholesale_price' => 420.00,
            'retail_price' => 540.00,
            'cost_price' => 290.00,
            'reorder_level' => 40,
            'is_active' => true,
        ]);

        $p1500 = Product::firstOrCreate(['sku' => 'QSW-1.5L-12'], [
            'name' => 'Quicksave Purified Water 1.5 Litre (Pack of 12)',
            'category' => 'Bottled Water',
            'unit_measure' => 'Pack of 12',
            'wholesale_price' => 480.00,
            'retail_price' => 620.00,
            'cost_price' => 330.00,
            'reorder_level' => 30,
            'is_active' => true,
        ]);

        $p5L = Product::firstOrCreate(['sku' => 'QSW-5L'], [
            'name' => 'Quicksave Purified Drinking Water 5 Litre Bottle',
            'category' => 'Bottled Water',
            'unit_measure' => 'Bottle',
            'wholesale_price' => 160.00,
            'retail_price' => 210.00,
            'cost_price' => 110.00,
            'reorder_level' => 30,
            'is_active' => true,
        ]);

        $p10L = Product::firstOrCreate(['sku' => 'QSW-10L'], [
            'name' => 'Quicksave Purified Drinking Water 10 Litre Bottle',
            'category' => 'Bottled Water',
            'unit_measure' => 'Bottle',
            'wholesale_price' => 290.00,
            'retail_price' => 370.00,
            'cost_price' => 190.00,
            'reorder_level' => 25,
            'is_active' => true,
        ]);

        $p189Refill = Product::firstOrCreate(['sku' => 'QSW-18.9L-REFILL'], [
            'name' => '18.9 Litre (5 Gallon) Purified Water Refill',
            'category' => 'Dispenser Refills',
            'unit_measure' => 'Refill Bottle',
            'wholesale_price' => 300.00,
            'retail_price' => 400.00,
            'cost_price' => 120.00,
            'reorder_level' => 100,
            'is_active' => true,
        ]);

        $p189New = Product::firstOrCreate(['sku' => 'QSW-18.9L-NEW'], [
            'name' => '18.9 Litre Dispenser Bottle (Complete with Water)',
            'category' => 'New Containers',
            'unit_measure' => 'Bottle',
            'wholesale_price' => 1400.00,
            'retail_price' => 1750.00,
            'cost_price' => 950.00,
            'reorder_level' => 20,
            'is_active' => true,
        ]);

        $pDispenser = Product::firstOrCreate(['sku' => 'QSW-DISP-ELEC'], [
            'name' => 'Quicksave Hot & Cold Compressor Water Dispenser',
            'category' => 'Dispensers & Equipment',
            'unit_measure' => 'Unit',
            'wholesale_price' => 14500.00,
            'retail_price' => 18000.00,
            'cost_price' => 11200.00,
            'reorder_level' => 5,
            'is_active' => true,
        ]);

        // 3. Store Inventory Quantities
        $inventoryData = [
            [$mainPlant->id, $p500->id, 320],
            [$mainPlant->id, $p1000->id, 180],
            [$mainPlant->id, $p1500->id, 140],
            [$mainPlant->id, $p5L->id, 95],
            [$mainPlant->id, $p10L->id, 65],
            [$mainPlant->id, $p189Refill->id, 450],
            [$mainPlant->id, $p189New->id, 45],
            [$mainPlant->id, $pDispenser->id, 12],
            [$depotWest->id, $p500->id, 110],
            [$depotWest->id, $p189Refill->id, 180],
            [$vanMobile1->id, $p189Refill->id, 60],
            [$vanMobile1->id, $p500->id, 35],
        ];

        foreach ($inventoryData as [$sId, $pId, $qty]) {
            StoreInventory::firstOrCreate(
                ['store_id' => $sId, 'product_id' => $pId],
                ['quantity' => $qty]
            );
        }

        // 4. Initial Audit Movements (matching the screenshot)
        $movements = [
            [
                'product_id' => $p189Refill->id,
                'store_id' => $mainPlant->id,
                'type' => 'PRODUCTION',
                'reference_type' => 'Batch',
                'reference_no' => 'BAT-2026-104',
                'quantity_change' => 200,
                'balance_before' => 250,
                'balance_after' => 450,
                'user_name' => 'James Mwangi (Plant Mgr)',
                'notes' => 'Daily automated RO filling run #104',
            ],
            [
                'product_id' => $p500->id,
                'store_id' => $mainPlant->id,
                'type' => 'SALE',
                'reference_type' => 'POS',
                'reference_no' => 'POS-2026-081',
                'quantity_change' => -15,
                'balance_before' => 335,
                'balance_after' => 320,
                'user_name' => 'Cashier Factory Counter',
                'notes' => 'Cashier POS retail sale',
            ],
            [
                'product_id' => $p189Refill->id,
                'store_id' => $mainPlant->id,
                'type' => 'TRANSFER_OUT',
                'reference_type' => 'Transfer',
                'reference_no' => 'TRF-2026-029',
                'quantity_change' => -60,
                'balance_before' => 510,
                'balance_after' => 450,
                'user_name' => 'James Mwangi',
                'notes' => 'Dispatched to Route Van KDA 421B',
            ],
            [
                'product_id' => $p189Refill->id,
                'store_id' => $vanMobile1->id,
                'type' => 'TRANSFER_IN',
                'reference_type' => 'Transfer',
                'reference_no' => 'TRF-2026-029',
                'quantity_change' => 60,
                'balance_before' => 0,
                'balance_after' => 60,
                'user_name' => 'Peter Karanja',
                'notes' => 'Received from Superior Center for morning delivery',
            ],
        ];

        foreach ($movements as $mov) {
            StockMovement::firstOrCreate(
                ['reference_no' => $mov['reference_no'], 'product_id' => $mov['product_id'], 'store_id' => $mov['store_id'], 'type' => $mov['type']],
                $mov
            );
        }

        // 5. Raw Materials (Preforms, Caps, Labels, Packaging, Chemicals)
        $rmPreform500 = RawMaterial::firstOrCreate(['code' => 'RM-PREF-500'], [
            'name' => 'PET Preforms 500ml 18g (Clear)',
            'category' => 'Preforms',
            'unit' => 'pcs',
            'unit_cost' => 3.80,
            'current_stock' => 12500,
            'reorder_level' => 3000,
            'description' => 'Virgin PET resin preforms for 500ml bottle blow moulding',
            'is_active' => true,
        ]);

        $rmPreform189 = RawMaterial::firstOrCreate(['code' => 'RM-PREF-189'], [
            'name' => 'Polycarbonate / Heavy PET 18.9L Preforms 680g',
            'category' => 'Preforms',
            'unit' => 'pcs',
            'unit_cost' => 180.00,
            'current_stock' => 840,
            'reorder_level' => 200,
            'description' => 'Preforms for 5-gallon reusable water dispenser bottles',
            'is_active' => true,
        ]);

        $rmCap28 = RawMaterial::firstOrCreate(['code' => 'RM-CAP-28'], [
            'name' => 'Water Bottle Screw Caps 28mm Blue Tamper-Evident',
            'category' => 'Caps & Closures',
            'unit' => 'pcs',
            'unit_cost' => 1.20,
            'current_stock' => 18000,
            'reorder_level' => 5000,
            'description' => 'Food grade HDPE 28mm screw closures with seal ring',
            'is_active' => true,
        ]);

        $rmCapSmart = RawMaterial::firstOrCreate(['code' => 'RM-CAP-SMART'], [
            'name' => '5-Gallon Non-Spill Smart Snap Caps (with Foam Liner)',
            'category' => 'Caps & Closures',
            'unit' => 'pcs',
            'unit_cost' => 12.50,
            'current_stock' => 3400,
            'reorder_level' => 1000,
            'description' => 'Disposable sanitary smart snap caps for dispenser water cooler spears',
            'is_active' => true,
        ]);

        $rmLabel500 = RawMaterial::firstOrCreate(['code' => 'RM-LBL-500'], [
            'name' => 'Quicksave 500ml BOPP Wrap-Around Printed Labels',
            'category' => 'Labels',
            'unit' => 'pcs',
            'unit_cost' => 1.50,
            'current_stock' => 14000,
            'reorder_level' => 4000,
            'description' => 'Full-color gravure printed labels with KEBS standardization mark',
            'is_active' => true,
        ]);

        $rmShrinkFilm = RawMaterial::firstOrCreate(['code' => 'RM-FLM-SHRINK'], [
            'name' => 'LDPE Bundling Shrink Wrap Film 450mm (Roll 25kg)',
            'category' => 'Packaging Film',
            'unit' => 'rolls',
            'unit_cost' => 6400.00,
            'current_stock' => 38,
            'reorder_level' => 10,
            'description' => 'Heavy duty shrink film for 24-bottle bundle packaging machine',
            'is_active' => true,
        ]);

        $rmCarbon = RawMaterial::firstOrCreate(['code' => 'RM-FLT-CARBON'], [
            'name' => 'Active Carbon & 5-Micron Sediment Filter Cartridges (20" Jumbo)',
            'category' => 'Treatment Media',
            'unit' => 'pcs',
            'unit_cost' => 1850.00,
            'current_stock' => 24,
            'reorder_level' => 6,
            'description' => 'Pre-treatment filter elements for commercial RO system',
            'is_active' => true,
        ]);

        // 6. Material Purchases
        $matPurchase = MaterialPurchase::firstOrCreate(['purchase_no' => 'MP-2026-0041'], [
            'supplier_name' => 'Polypack Industries Ltd',
            'supplier_invoice_no' => 'INV-PLP-8921',
            'purchase_date' => now()->subDays(5)->toDateString(),
            'status' => 'Received',
            'total_amount' => 114000.00,
            'payment_status' => 'Paid',
            'notes' => 'Procurement of 20,000 500ml preforms and 30,000 blue caps',
        ]);

        MaterialPurchaseItem::firstOrCreate([
            'material_purchase_id' => $matPurchase->id,
            'raw_material_id' => $rmPreform500->id,
        ], [
            'quantity' => 20000,
            'unit_cost' => 3.80,
            'total_cost' => 76000.00,
        ]);

        MaterialPurchaseItem::firstOrCreate([
            'material_purchase_id' => $matPurchase->id,
            'raw_material_id' => $rmCap28->id,
        ], [
            'quantity' => 31666,
            'unit_cost' => 1.20,
            'total_cost' => 38000.00,
        ]);

        // 7. Production Batches
        $prodBatch1 = ProductionBatch::firstOrCreate(['batch_no' => 'BAT-2026-104'], [
            'product_id' => $p189Refill->id,
            'store_id' => $mainPlant->id,
            'planned_quantity' => 200,
            'actual_yield' => 198,
            'rejected_quantity' => 2,
            'batch_date' => now()->subDays(1)->toDateString(),
            'status' => 'Completed',
            'production_cost' => 23760.00,
            'supervisor_name' => 'Dennis Kipchumba',
            'shift' => 'Day Shift (07:00 - 16:00)',
            'water_source_reading' => 'Initial RO Meter: 48,200 L; Final: 51,980 L (Yield: 3,762 Litres)',
            'notes' => 'Passed full TDS test (32 ppm) & microbial UV chamber test.',
        ]);

        ProductionMaterialUsage::firstOrCreate([
            'production_batch_id' => $prodBatch1->id,
            'raw_material_id' => $rmCapSmart->id,
        ], [
            'quantity_used' => 200,
            'quantity_wasted' => 2,
            'unit_cost' => 12.50,
            'total_cost' => 2525.00,
        ]);

        $prodBatch2 = ProductionBatch::firstOrCreate(['batch_no' => 'BAT-2026-105'], [
            'product_id' => $p500->id,
            'store_id' => $mainPlant->id,
            'planned_quantity' => 150, // 150 packs x 24 = 3,600 bottles
            'actual_yield' => 148,
            'rejected_quantity' => 2,
            'batch_date' => now()->toDateString(),
            'status' => 'Completed',
            'production_cost' => 47360.00,
            'supervisor_name' => 'Dennis Kipchumba',
            'shift' => 'Morning Shift',
            'water_source_reading' => 'Initial RO Meter: 51,980 L; Final: 53,780 L',
            'notes' => 'Packaging run for Chandarana supermarkets order',
        ]);

        ProductionMaterialUsage::firstOrCreate([
            'production_batch_id' => $prodBatch2->id,
            'raw_material_id' => $rmPreform500->id,
        ], [
            'quantity_used' => 3600,
            'quantity_wasted' => 18,
            'unit_cost' => 3.80,
            'total_cost' => 13748.40,
        ]);

        ProductionMaterialUsage::firstOrCreate([
            'production_batch_id' => $prodBatch2->id,
            'raw_material_id' => $rmCap28->id,
        ], [
            'quantity_used' => 3600,
            'quantity_wasted' => 12,
            'unit_cost' => 1.20,
            'total_cost' => 4334.40,
        ]);

        // 8. Fixed Asset Register
        $assets = [
            [
                'asset_code' => 'FA-RO-001',
                'name' => 'Commercial Multi-Stage Reverse Osmosis (RO) Purification Plant 4000 LPH',
                'category' => 'Water Treatment & RO Plant',
                'serial_no' => 'RO-VONTRON-2024-88',
                'purchase_date' => '2024-03-15',
                'purchase_cost' => 2850000.00,
                'salvage_value' => 200000.00,
                'useful_life_years' => 10,
                'depreciation_method' => 'Straight-Line',
                'annual_depreciation_rate' => 10.00,
                'accumulated_depreciation' => 684500.00,
                'current_book_value' => 2165500.00,
                'location' => 'Main Plant Hall - Section A',
                'assigned_to' => 'Chief Operations Engineer',
                'status' => 'Operational',
                'last_maintenance_date' => '2026-09-10',
                'next_maintenance_date' => '2026-11-10',
                'notes' => 'Quartz sand, active carbon, water softener & 8x Vontron 8040 membranes.',
            ],
            [
                'asset_code' => 'FA-BOT-002',
                'name' => 'Automatic 3-in-1 Rinsing, Filling & Capping Monoblock Line (3000 BPH)',
                'category' => 'Bottling & Packaging Line',
                'serial_no' => 'RFC-AUTO-9014',
                'purchase_date' => '2024-05-20',
                'purchase_cost' => 3200000.00,
                'salvage_value' => 300000.00,
                'useful_life_years' => 8,
                'depreciation_method' => 'Straight-Line',
                'annual_depreciation_rate' => 12.50,
                'accumulated_depreciation' => 848000.00,
                'current_book_value' => 2352000.00,
                'location' => 'Bottling Room - Clean Zone',
                'assigned_to' => 'Bottling Line Technician',
                'status' => 'Operational',
                'last_maintenance_date' => '2026-09-25',
                'next_maintenance_date' => '2026-10-25',
                'notes' => 'Rotary bottle gripper, stainless steel 316L liquid contact parts.',
            ],
            [
                'asset_code' => 'FA-TRK-001',
                'name' => 'Isuzu FRR 9-Tonne Commercial Water Distribution Truck (KBZ 740W)',
                'category' => 'Motor Vehicles & Trucks',
                'serial_no' => 'ISZ-FRR-47291',
                'purchase_date' => '2023-08-10',
                'purchase_cost' => 5400000.00,
                'salvage_value' => 800000.00,
                'useful_life_years' => 7,
                'depreciation_method' => 'Reducing Balance',
                'annual_depreciation_rate' => 20.00,
                'accumulated_depreciation' => 2480000.00,
                'current_book_value' => 2920000.00,
                'location' => 'Logistics Fleet Bay',
                'assigned_to' => 'Head Driver - Samuel Gitonga',
                'status' => 'Operational',
                'last_maintenance_date' => '2026-09-18',
                'next_maintenance_date' => '2026-12-18',
                'notes' => 'Insured comprehensively with Britam Kenya. Custom cage body for 5-gal bottles.',
            ],
            [
                'asset_code' => 'FA-DSP-POOL',
                'name' => 'Corporate Loaned Dispenser Pool (85 Units Active with Clients)',
                'category' => 'Field Dispensers (Leased/Rented)',
                'serial_no' => 'QSW-POOL-001 to 085',
                'purchase_date' => '2025-01-10',
                'purchase_cost' => 1020000.00,
                'salvage_value' => 100000.00,
                'useful_life_years' => 4,
                'depreciation_method' => 'Straight-Line',
                'annual_depreciation_rate' => 25.00,
                'accumulated_depreciation' => 433500.00,
                'current_book_value' => 586500.00,
                'location' => 'Nairobi Corporate Client Offices',
                'assigned_to' => 'Field Client Service Team',
                'status' => 'Operational',
                'last_maintenance_date' => '2026-08-15',
                'next_maintenance_date' => '2026-11-15',
                'notes' => 'Quarterly sanitization & filter servicing schedule adhered to.',
            ],
        ];

        foreach ($assets as $asset) {
            FixedAsset::firstOrCreate(['asset_code' => $asset['asset_code']], $asset);
        }

        // 9. Customers (AR)
        $c1 = Customer::firstOrCreate(['code' => 'CUST-001'], [
            'name' => 'Chandarana Foodplus Supermarkets Ltd',
            'customer_type' => 'Retail/Supermarket',
            'phone' => '+254 720 334 455',
            'email' => 'orders@chandaranasupermarkets.com',
            'address' => 'Diamond Plaza, Parklands, Nairobi',
            'tax_pin' => 'P051289192M',
            'credit_limit' => 350000.00,
            'payment_terms_days' => 30,
            'opening_balance' => 0.00,
            'current_balance' => 67500.00,
            'is_active' => true,
        ]);

        $c2 = Customer::firstOrCreate(['code' => 'CUST-002'], [
            'name' => 'Equity Bank Towers Headquarters',
            'customer_type' => 'Corporate',
            'phone' => '+254 763 000 000',
            'email' => 'facilities@equitybank.co.ke',
            'address' => 'Equity Centre, Hospital Rd, Upper Hill, Nairobi',
            'tax_pin' => 'P000624911K',
            'credit_limit' => 200000.00,
            'payment_terms_days' => 30,
            'opening_balance' => 0.00,
            'current_balance' => 36000.00,
            'is_active' => true,
        ]);

        $c3 = Customer::firstOrCreate(['code' => 'CUST-003'], [
            'name' => 'Naivas Supermarkets Westlands',
            'customer_type' => 'Retail/Supermarket',
            'phone' => '+254 711 200 300',
            'email' => 'procurement.westlands@naivas.co.ke',
            'address' => 'The Mall, Westlands, Nairobi',
            'tax_pin' => 'P051189914L',
            'credit_limit' => 500000.00,
            'payment_terms_days' => 45,
            'opening_balance' => 0.00,
            'current_balance' => 112000.00,
            'is_active' => true,
        ]);

        $c4 = Customer::firstOrCreate(['code' => 'CUST-004'], [
            'name' => 'Serena Hotel Nairobi',
            'customer_type' => 'Corporate',
            'phone' => '+254 732 123 333',
            'email' => 'purchasing@serenahotels.com',
            'address' => 'Kenyatta Ave, Central Park, Nairobi',
            'tax_pin' => 'P051201994A',
            'credit_limit' => 250000.00,
            'payment_terms_days' => 30,
            'opening_balance' => 0.00,
            'current_balance' => 0.00,
            'is_active' => true,
        ]);

        $cPos = Customer::firstOrCreate(['code' => 'CUST-WALKIN'], [
            'name' => 'Factory Gate Walk-in Customer (Cash/POS)',
            'customer_type' => 'Walk-In/Refill',
            'phone' => '+254 700 000 000',
            'email' => 'pos@quicksavewater.co.ke',
            'address' => 'Factory Dispatch Counter',
            'tax_pin' => 'N/A',
            'credit_limit' => 0.00,
            'payment_terms_days' => 0,
            'opening_balance' => 0.00,
            'current_balance' => 0.00,
            'is_active' => true,
        ]);

        // 10. Vendors (AP)
        $v1 = Vendor::firstOrCreate(['code' => 'VND-001'], [
            'name' => 'Polypack Industries Ltd',
            'category' => 'Raw Materials',
            'phone' => '+254 722 554 433',
            'email' => 'sales@polypack.co.ke',
            'address' => 'Enterprise Road, Industrial Area, Nairobi',
            'tax_pin' => 'P051009871T',
            'payment_terms_days' => 30,
            'opening_balance' => 0.00,
            'current_balance' => 114000.00,
            'is_active' => true,
        ]);

        $v2 = Vendor::firstOrCreate(['code' => 'VND-002'], [
            'name' => 'CleanWater Technologies & RO Solutions',
            'category' => 'Machinery & Spares',
            'phone' => '+254 733 908 112',
            'email' => 'support@cleanwatertech.co.ke',
            'address' => 'Mombasa Road, Syokimau, Nairobi',
            'tax_pin' => 'P051187622G',
            'payment_terms_days' => 15,
            'opening_balance' => 0.00,
            'current_balance' => 45000.00,
            'is_active' => true,
        ]);

        $v3 = Vendor::firstOrCreate(['code' => 'VND-003'], [
            'name' => 'Label Express Kenya Ltd',
            'category' => 'Raw Materials',
            'phone' => '+254 712 345 987',
            'email' => 'orders@labelexpress.co.ke',
            'address' => 'Baba Dogo Road, Ruaraka, Nairobi',
            'tax_pin' => 'P051399811C',
            'payment_terms_days' => 30,
            'opening_balance' => 0.00,
            'current_balance' => 0.00,
            'is_active' => true,
        ]);

        // 11. Sales Pipeline: Quotations, Sales Orders, Delivery Notes, Invoices & Receipts
        $quote1 = Quotation::firstOrCreate(['quote_no' => 'QT-2026-0045'], [
            'customer_id' => $c1->id,
            'quote_date' => now()->subDays(10)->toDateString(),
            'valid_until' => now()->addDays(20)->toDateString(),
            'subtotal' => 67500.00,
            'tax_amount' => 10800.00,
            'discount_amount' => 0.00,
            'grand_total' => 78300.00,
            'status' => 'Converted',
            'notes' => 'Quotation for 150 packs 500ml purified water',
            'created_by' => 'Sales Executive',
        ]);

        QuotationItem::firstOrCreate([
            'quotation_id' => $quote1->id,
            'product_id' => $p500->id,
        ], [
            'quantity' => 150,
            'unit_price' => 450.00,
            'discount' => 0,
            'tax_amount' => 10800.00,
            'total' => 78300.00,
        ]);

        $so1 = SalesOrder::firstOrCreate(['order_no' => 'SO-2026-0038'], [
            'customer_id' => $c1->id,
            'quotation_id' => $quote1->id,
            'order_date' => now()->subDays(8)->toDateString(),
            'delivery_due_date' => now()->subDays(6)->toDateString(),
            'subtotal' => 67500.00,
            'tax_amount' => 10800.00,
            'grand_total' => 78300.00,
            'status' => 'Delivered',
            'notes' => 'Approved LPO #CHN-9021 from Chandarana Foodplus',
        ]);

        SalesOrderItem::firstOrCreate([
            'sales_order_id' => $so1->id,
            'product_id' => $p500->id,
        ], [
            'quantity' => 150,
            'unit_price' => 450.00,
            'tax_amount' => 10800.00,
            'total' => 78300.00,
        ]);

        $dn1 = DeliveryNote::firstOrCreate(['delivery_no' => 'DN-2026-0031'], [
            'sales_order_id' => $so1->id,
            'customer_id' => $c1->id,
            'store_id' => $mainPlant->id,
            'delivery_date' => now()->subDays(6)->toDateString(),
            'driver_name' => 'Samuel Gitonga',
            'vehicle_reg' => 'KBZ 740W',
            'status' => 'Signed',
            'notes' => 'Delivered to Chandarana Central Distribution Warehouse.',
        ]);

        DeliveryNoteItem::firstOrCreate([
            'delivery_note_id' => $dn1->id,
            'product_id' => $p500->id,
        ], [
            'quantity_ordered' => 150,
            'quantity_delivered' => 150,
        ]);

        $inv1 = SalesInvoice::firstOrCreate(['invoice_no' => 'INV-2026-0049'], [
            'customer_id' => $c1->id,
            'store_id' => $mainPlant->id,
            'sales_order_id' => $so1->id,
            'invoice_date' => now()->subDays(6)->toDateString(),
            'due_date' => now()->addDays(24)->toDateString(),
            'subtotal' => 67500.00,
            'tax_amount' => 10800.00,
            'discount_amount' => 0.00,
            'grand_total' => 78300.00,
            'paid_amount' => 0.00,
            'balance_due' => 78300.00,
            'payment_method' => 'Credit',
            'payment_status' => 'Unpaid',
            'is_pos' => false,
            'cashier_name' => 'Billing Officer',
            'notes' => 'Net 30 Days commercial terms.',
        ]);

        SalesInvoiceItem::firstOrCreate([
            'sales_invoice_id' => $inv1->id,
            'product_id' => $p500->id,
        ], [
            'quantity' => 150,
            'unit_price' => 450.00,
            'discount' => 0,
            'tax_amount' => 10800.00,
            'total' => 78300.00,
        ]);

        // Point of Sale (POS) Transactions
        $posInv1 = SalesInvoice::firstOrCreate(['invoice_no' => 'POS-2026-0081'], [
            'customer_id' => $cPos->id,
            'store_id' => $mainPlant->id,
            'sales_order_id' => null,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->toDateString(),
            'subtotal' => 7100.00,
            'tax_amount' => 0.00,
            'discount_amount' => 0.00,
            'grand_total' => 7100.00,
            'paid_amount' => 7100.00,
            'balance_due' => 0.00,
            'payment_method' => 'Mpesa',
            'payment_status' => 'Paid',
            'is_pos' => true,
            'cashier_name' => 'James Mwangi',
            'notes' => 'Counter POS Refill + Bottle Sale. M-Pesa Code: QA84920482',
        ]);

        SalesInvoiceItem::firstOrCreate([
            'sales_invoice_id' => $posInv1->id,
            'product_id' => $p189Refill->id,
        ], [
            'quantity' => 15,
            'unit_price' => 400.00,
            'discount' => 0,
            'tax_amount' => 0,
            'total' => 6000.00,
        ]);

        SalesInvoiceItem::firstOrCreate([
            'sales_invoice_id' => $posInv1->id,
            'product_id' => $p500->id,
        ], [
            'quantity' => 2,
            'unit_price' => 550.00,
            'discount' => 0,
            'tax_amount' => 0,
            'total' => 1100.00,
        ]);

        CustomerReceipt::firstOrCreate(['receipt_no' => 'RCP-2026-0081'], [
            'customer_id' => $cPos->id,
            'sales_invoice_id' => $posInv1->id,
            'receipt_date' => now()->toDateString(),
            'amount' => 7100.00,
            'payment_method' => 'Mpesa',
            'reference_no' => 'QA84920482',
            'receipt_type' => 'Direct Receipt',
            'received_by' => 'James Mwangi',
            'notes' => 'Instant POS Settlement',
        ]);

        // 12. Payables Flow: Requisitions, PO, GRN, Invoices, Payment Vouchers
        $pr1 = PurchaseRequisition::firstOrCreate(['requisition_no' => 'PR-2026-0019'], [
            'department' => 'Production & Bottling',
            'requested_by' => 'Dennis Kipchumba',
            'requisition_date' => now()->subDays(12)->toDateString(),
            'required_date' => now()->subDays(6)->toDateString(),
            'estimated_total' => 114000.00,
            'priority' => 'High',
            'status' => 'PO Created',
            'purpose' => 'Replenish 500ml PET preforms & 28mm caps for October production run',
        ]);

        PurchaseRequisitionItem::firstOrCreate([
            'purchase_requisition_id' => $pr1->id,
            'item_description' => 'PET Preforms 500ml 18g Clear',
        ], [
            'quantity' => 20000,
            'unit' => 'pcs',
            'estimated_unit_cost' => 3.80,
            'estimated_total' => 76000.00,
        ]);

        $po1 = PurchaseOrder::firstOrCreate(['po_no' => 'PO-2026-0014'], [
            'vendor_id' => $v1->id,
            'purchase_requisition_id' => $pr1->id,
            'order_date' => now()->subDays(10)->toDateString(),
            'expected_delivery_date' => now()->subDays(5)->toDateString(),
            'subtotal' => 114000.00,
            'tax_amount' => 0.00,
            'grand_total' => 114000.00,
            'status' => 'Completed',
            'notes' => 'Standard supply contract #PLP-2026',
        ]);

        PurchaseOrderItem::firstOrCreate([
            'purchase_order_id' => $po1->id,
            'item_description' => 'PET Preforms 500ml 18g Clear',
        ], [
            'quantity' => 20000,
            'unit_price' => 3.80,
            'tax_amount' => 0,
            'total' => 76000.00,
        ]);

        $grn1 = GoodsReceivedNote::firstOrCreate(['grn_no' => 'GRN-2026-0012'], [
            'purchase_order_id' => $po1->id,
            'vendor_id' => $v1->id,
            'store_id' => $mainPlant->id,
            'received_date' => now()->subDays(5)->toDateString(),
            'delivery_note_ref' => 'PLP-DN-7822',
            'received_by' => 'Storekeeper Peter Karanja',
            'status' => 'Verified',
            'notes' => 'Inspected 20,000 preforms. No deformities or contamination.',
        ]);

        GoodsReceivedNoteItem::firstOrCreate([
            'goods_received_note_id' => $grn1->id,
            'item_description' => 'PET Preforms 500ml 18g Clear',
        ], [
            'quantity_ordered' => 20000,
            'quantity_received' => 20000,
            'quantity_accepted' => 20000,
            'quantity_rejected' => 0,
            'unit_cost' => 3.80,
        ]);

        $pinv1 = PurchaseInvoice::firstOrCreate(['invoice_no' => 'PINV-2026-0018'], [
            'vendor_invoice_no' => 'INV-PLP-8921',
            'vendor_id' => $v1->id,
            'goods_received_note_id' => $grn1->id,
            'purchase_order_id' => $po1->id,
            'invoice_date' => now()->subDays(5)->toDateString(),
            'due_date' => now()->addDays(25)->toDateString(),
            'subtotal' => 114000.00,
            'tax_amount' => 0.00,
            'grand_total' => 114000.00,
            'paid_amount' => 0.00,
            'balance_due' => 114000.00,
            'payment_status' => 'Unpaid',
            'notes' => 'Net 30 days invoice.',
        ]);

        PurchaseInvoiceItem::firstOrCreate([
            'purchase_invoice_id' => $pinv1->id,
            'description' => 'PET Preforms 500ml 18g Clear',
        ], [
            'quantity' => 20000,
            'unit_price' => 3.80,
            'tax_amount' => 0,
            'total' => 76000.00,
        ]);

        // 13. Bank Accounts, Cashbooks, Mpesa & Reconciliation
        $bkKcb = BankAccount::firstOrCreate(['account_number' => '1192837465'], [
            'account_name' => 'KCB Main Operating Account',
            'account_type' => 'Bank',
            'bank_name' => 'Kenya Commercial Bank',
            'branch' => 'Industrial Area Branch',
            'opening_balance' => 850000.00,
            'current_balance' => 1245800.00,
            'currency' => 'KES',
            'is_active' => true,
        ]);

        $bkPetty = BankAccount::firstOrCreate(['account_name' => 'Factory Petty Cashbook Float'], [
            'account_type' => 'Petty Cash',
            'bank_name' => 'Internal Safe Box',
            'account_number' => 'FLOAT-PC-01',
            'branch' => 'Head Cashier Office',
            'opening_balance' => 50000.00,
            'current_balance' => 32450.00,
            'currency' => 'KES',
            'is_active' => true,
        ]);

        $bkMpesaPaybill = BankAccount::firstOrCreate(['account_number' => '400200'], [
            'account_name' => 'M-Pesa Corporate Paybill (400200)',
            'account_type' => 'M-Pesa Paybill',
            'bank_name' => 'Safaricom M-Pesa',
            'branch' => 'Headquarters',
            'opening_balance' => 120000.00,
            'current_balance' => 285430.00,
            'currency' => 'KES',
            'is_active' => true,
        ]);

        $bkMpesaTill = BankAccount::firstOrCreate(['account_number' => '543210'], [
            'account_name' => 'Factory Gate POS M-Pesa Till (543210)',
            'account_type' => 'M-Pesa Till',
            'bank_name' => 'Safaricom Buy Goods Till',
            'branch' => 'Superior Center Counter',
            'opening_balance' => 35000.00,
            'current_balance' => 96320.00,
            'currency' => 'KES',
            'is_active' => true,
        ]);

        // Cashbook Transactions
        CashTransaction::firstOrCreate(['txn_no' => 'PC-2026-0042'], [
            'bank_account_id' => $bkPetty->id,
            'txn_date' => now()->subDays(2)->toDateString(),
            'type' => 'OUTFLOW',
            'category' => 'Fuel & Transport',
            'amount' => 4500.00,
            'payee_or_payer' => 'Samuel Gitonga (Driver)',
            'reference_no' => 'PETTY-042',
            'description' => 'Emergency diesel top-up for delivery truck KBZ 740W',
            'approved_by' => 'James Mwangi',
            'is_reconciled' => true,
        ]);

        CashTransaction::firstOrCreate(['txn_no' => 'PC-2026-0043'], [
            'bank_account_id' => $bkPetty->id,
            'txn_date' => now()->subDays(1)->toDateString(),
            'type' => 'OUTFLOW',
            'category' => 'Staff Amenities & Meals',
            'amount' => 2850.00,
            'payee_or_payer' => 'Mama Wanjiku Catering',
            'reference_no' => 'PETTY-043',
            'description' => 'Bottling line night-shift worker overtime meal allowances',
            'approved_by' => 'Dennis Kipchumba',
            'is_reconciled' => true,
        ]);

        CashTransaction::firstOrCreate(['txn_no' => 'MC-2026-0012'], [
            'bank_account_id' => $bkKcb->id,
            'txn_date' => now()->toDateString(),
            'type' => 'INFLOW',
            'category' => 'Sales Collection',
            'amount' => 71320.00,
            'payee_or_payer' => 'Daily Direct Cash & Till Settlement',
            'reference_no' => 'DEP-2026-1008',
            'description' => 'Direct deposit of accumulated counter receipts into KCB',
            'approved_by' => 'Finance Director',
            'is_reconciled' => true,
        ]);

        // M-Pesa Transactions
        MpesaTransaction::firstOrCreate(['mpesa_receipt_no' => 'QA84920482'], [
            'bank_account_id' => $bkMpesaTill->id,
            'type' => 'Till',
            'shortcode' => '543210',
            'phone_number' => '254722889900',
            'customer_name' => 'Kennedy Mutua',
            'amount' => 7100.00,
            'transaction_time' => now()->subHours(3),
            'account_reference' => 'POS-2026-0081',
            'status' => 'Allocated',
            'allocated_to_type' => 'SalesInvoice',
            'allocated_to_id' => $posInv1->id,
        ]);

        MpesaTransaction::firstOrCreate(['mpesa_receipt_no' => 'QA91029411'], [
            'bank_account_id' => $bkMpesaPaybill->id,
            'type' => 'Paybill',
            'shortcode' => '400200',
            'phone_number' => '254711445566',
            'customer_name' => 'Sarah Nduta',
            'amount' => 3600.00,
            'transaction_time' => now()->subHours(5),
            'account_reference' => 'CUST-002',
            'status' => 'Allocated',
            'allocated_to_type' => 'Customer',
            'allocated_to_id' => $c2->id,
        ]);

        MpesaTransaction::firstOrCreate(['mpesa_receipt_no' => 'QA99482103'], [
            'bank_account_id' => $bkMpesaPaybill->id,
            'type' => 'Paybill',
            'shortcode' => '400200',
            'phone_number' => '254720998877',
            'customer_name' => 'Paul Kiprono',
            'amount' => 1200.00,
            'transaction_time' => now()->subHours(1),
            'account_reference' => 'REFILL-18.9L',
            'status' => 'Unallocated',
            'allocated_to_type' => null,
            'allocated_to_id' => null,
        ]);

        // Bank Reconciliation
        $rec = BankReconciliation::firstOrCreate(['reconciliation_no' => 'BRC-2026-09'], [
            'bank_account_id' => $bkKcb->id,
            'statement_date' => '2026-09-30',
            'statement_ending_balance' => 1245800.00,
            'book_balance' => 1245800.00,
            'cleared_balance' => 1245800.00,
            'difference' => 0.00,
            'status' => 'Reconciled',
            'reconciled_by' => 'James Mwangi',
            'notes' => 'September Bank statement matched 100% against KCB cashbook ledger.',
        ]);

        BankStatementLine::firstOrCreate([
            'bank_reconciliation_id' => $rec->id,
            'reference' => 'KCB-STMT-901',
        ], [
            'line_date' => '2026-09-28',
            'description' => 'RTGS Inward: Serena Hotel Water Invoice Payment',
            'withdrawal' => 0.00,
            'deposit' => 150000.00,
            'running_balance' => 1245800.00,
            'is_matched' => true,
            'matched_ref' => 'RCP-2026-0045',
        ]);

        // 14. Chart of Accounts & General Ledger
        $coaAccounts = [
            ['1010', 'Main Operating Bank (KCB)', 'Asset', 'Current Asset', 'Debit', 1245800.00],
            ['1020', 'Petty Cashbook Float', 'Asset', 'Current Asset', 'Debit', 32450.00],
            ['1030', 'M-Pesa Collections (Paybill & Till)', 'Asset', 'Current Asset', 'Debit', 381750.00],
            ['1100', 'Accounts Receivable (Trade Debtors)', 'Asset', 'Current Asset', 'Debit', 215500.00],
            ['1200', 'Finished Goods Inventory', 'Asset', 'Current Asset', 'Debit', 1542430.00],
            ['1250', 'Raw Materials & Packaging Inventory', 'Asset', 'Current Asset', 'Debit', 428600.00],
            ['1500', 'Plant, Machinery & Vehicles (Fixed Assets)', 'Asset', 'Fixed Asset', 'Debit', 8024000.00],
            ['2000', 'Accounts Payable (Trade Creditors)', 'Liability', 'Current Liability', 'Credit', 159000.00],
            ['2100', 'VAT / Withholding Tax Payable', 'Liability', 'Current Liability', 'Credit', 42800.00],
            ['3000', 'Share Capital & Paid-in Equity', 'Equity', 'Equity', 'Credit', 9500000.00],
            ['3100', 'Retained Earnings', 'Equity', 'Equity', 'Credit', 1788730.00],
            ['4000', 'Drinking Water Sales Revenue', 'Revenue', 'Operating Revenue', 'Credit', 713200.00],
            ['5000', 'Cost of Goods Sold (Water Production & Materials)', 'Expense', 'Direct Cost', 'Debit', 382400.00],
            ['6000', 'Plant Utilities & Three-Phase Power', 'Expense', 'Admin Expense', 'Debit', 95400.00],
            ['6100', 'Fleet Fuel, Maintenance & Logistics', 'Expense', 'Admin Expense', 'Debit', 68300.00],
            ['6200', 'Factory Salaries & Operational Wages', 'Expense', 'Admin Expense', 'Debit', 145000.00],
        ];

        foreach ($coaAccounts as [$code, $name, $type, $sub, $normal, $bal]) {
            ChartOfAccount::firstOrCreate(['code' => $code], [
                'name' => $name,
                'type' => $type,
                'sub_category' => $sub,
                'normal_balance' => $normal,
                'opening_balance' => $bal,
                'current_balance' => $bal,
                'is_active' => true,
            ]);
        }

        // Sample General Ledger Journal Entry
        $je = JournalEntry::firstOrCreate(['entry_no' => 'JE-2026-0001'], [
            'entry_date' => now()->toDateString(),
            'reference_type' => 'POS',
            'reference_no' => 'POS-2026-0081',
            'description' => 'Posting daily factory gate POS cash & M-Pesa sales',
            'total_debit' => 7100.00,
            'total_credit' => 7100.00,
            'posted_by' => 'James Mwangi',
        ]);

        $acctMpesa = ChartOfAccount::where('code', '1030')->first();
        $acctRev = ChartOfAccount::where('code', '4000')->first();

        if ($acctMpesa && $acctRev) {
            JournalEntryLine::firstOrCreate([
                'journal_entry_id' => $je->id,
                'account_id' => $acctMpesa->id,
            ], [
                'debit' => 7100.00,
                'credit' => 0.00,
                'narration' => 'Mpesa collections debit',
            ]);

            JournalEntryLine::firstOrCreate([
                'journal_entry_id' => $je->id,
                'account_id' => $acctRev->id,
            ], [
                'debit' => 0.00,
                'credit' => 7100.00,
                'narration' => 'Water sales credit',
            ]);
        }
    }
}
