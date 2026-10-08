<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('type')->default('Warehouse'); // Warehouse, Depot, Van Store, Retail Outlet
            $table->string('location')->nullable();
            $table->string('manager_name')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('name');
            $table->string('category'); // Bottled Water, Refills, Empty Bottles, Dispensers & Equipment
            $table->string('unit_measure')->default('Case (24)'); // Case (24), Case (12), Unit, Refill Bottle
            $table->decimal('wholesale_price', 12, 2)->default(0);
            $table->decimal('retail_price', 12, 2)->default(0);
            $table->decimal('cost_price', 12, 2)->default(0);
            $table->integer('reorder_level')->default(20);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('store_inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('quantity')->default(0);
            $table->timestamps();
            $table->unique(['store_id', 'product_id']);
        });

        Schema::create('store_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_no')->unique();
            $table->foreignId('from_store_id')->constrained('stores');
            $table->foreignId('to_store_id')->constrained('stores');
            $table->date('transfer_date');
            $table->string('status')->default('Completed'); // Pending, Dispatched, Completed, Cancelled
            $table->string('driver_name')->nullable();
            $table->string('vehicle_reg')->nullable();
            $table->text('notes')->nullable();
            $table->string('created_by')->default('Admin');
            $table->timestamps();
        });

        Schema::create('store_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_transfer_id')->constrained('store_transfers')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products');
            $table->integer('quantity_sent');
            $table->integer('quantity_received')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('adjustment_no')->unique();
            $table->foreignId('store_id')->constrained('stores');
            $table->date('adjustment_date');
            $table->string('type'); // ADDITION, DEDUCTION
            $table->string('reason'); // Damage, Spillage, Expiry, Physical Count Variance, Sample
            $table->decimal('total_value', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->string('created_by')->default('Admin');
            $table->timestamps();
        });

        Schema::create('stock_adjustment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_adjustment_id')->constrained('stock_adjustments')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products');
            $table->integer('quantity');
            $table->decimal('unit_cost', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('store_id')->constrained('stores');
            $table->string('type'); // PRODUCTION, SALE, TRANSFER_IN, TRANSFER_OUT, ADJUSTMENT_ADD, ADJUSTMENT_SUB, PURCHASE
            $table->string('reference_type')->nullable(); // Batch, SaleInvoice, Transfer, Adjustment
            $table->string('reference_no')->nullable();
            $table->integer('quantity_change');
            $table->integer('balance_before');
            $table->integer('balance_after');
            $table->string('user_name')->default('Admin');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('stock_adjustment_items');
        Schema::dropIfExists('stock_adjustments');
        Schema::dropIfExists('store_transfer_items');
        Schema::dropIfExists('store_transfers');
        Schema::dropIfExists('store_inventory');
        Schema::dropIfExists('products');
        Schema::dropIfExists('stores');
    }
};
