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
        Schema::create('raw_materials', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('category')->default('Packaging'); // Preforms, Caps, Labels, Packaging Film, Cartons, Treatment Chemicals, Filter Media
            $table->string('unit')->default('pcs'); // pcs, rolls, kg, liters, boxes
            $table->decimal('unit_cost', 12, 2)->default(0);
            $table->decimal('current_stock', 12, 2)->default(0);
            $table->decimal('reorder_level', 12, 2)->default(100);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('material_purchases', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_no')->unique();
            $table->string('supplier_name');
            $table->string('supplier_invoice_no')->nullable();
            $table->date('purchase_date');
            $table->string('status')->default('Received'); // Received, Pending, Cancelled
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('payment_status')->default('Paid'); // Paid, Partial, Unpaid
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('material_purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_purchase_id')->constrained('material_purchases')->cascadeOnDelete();
            $table->foreignId('raw_material_id')->constrained('raw_materials');
            $table->decimal('quantity', 12, 2);
            $table->decimal('unit_cost', 12, 2);
            $table->decimal('total_cost', 12, 2);
            $table->timestamps();
        });

        Schema::create('production_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_no')->unique();
            $table->foreignId('product_id')->constrained('products'); // Finished product being produced
            $table->foreignId('store_id')->constrained('stores'); // Store/Warehouse receiving finished goods
            $table->integer('planned_quantity');
            $table->integer('actual_yield')->default(0);
            $table->integer('rejected_quantity')->default(0);
            $table->date('batch_date');
            $table->string('status')->default('Completed'); // Draft, In Progress, Completed, Cancelled
            $table->decimal('production_cost', 12, 2)->default(0);
            $table->string('supervisor_name')->default('Production Supervisor');
            $table->string('shift')->default('Day Shift');
            $table->text('water_source_reading')->nullable(); // e.g. RO Meter: 12,500 L treated
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('production_material_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_batch_id')->constrained('production_batches')->cascadeOnDelete();
            $table->foreignId('raw_material_id')->constrained('raw_materials');
            $table->decimal('quantity_used', 12, 2);
            $table->decimal('quantity_wasted', 12, 2)->default(0);
            $table->decimal('unit_cost', 12, 2)->default(0);
            $table->decimal('total_cost', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_material_usages');
        Schema::dropIfExists('production_batches');
        Schema::dropIfExists('material_purchase_items');
        Schema::dropIfExists('material_purchases');
        Schema::dropIfExists('raw_materials');
    }
};
