<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fixed_assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code')->unique();
            $table->string('name');
            $table->string('category');
            $table->string('serial_no')->nullable();
            $table->date('purchase_date');
            $table->decimal('purchase_cost', 14, 2);
            $table->decimal('salvage_value', 14, 2)->default(0);
            $table->integer('useful_life_years')->default(5);
            $table->string('depreciation_method')->default('Straight-Line');
            $table->decimal('annual_depreciation_rate', 5, 2)->default(20.00);
            $table->decimal('accumulated_depreciation', 14, 2)->default(0);
            $table->decimal('current_book_value', 14, 2);
            $table->string('location')->default('Factory Main Plant');
            $table->string('assigned_to')->nullable();
            $table->string('status')->default('Operational');
            $table->date('last_maintenance_date')->nullable();
            $table->date('next_maintenance_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fixed_assets');
    }
};
