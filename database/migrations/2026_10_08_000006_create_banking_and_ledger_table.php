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
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_name');
            $table->string('account_type'); // Bank, Petty Cash, M-Pesa Paybill, M-Pesa Till, Cash
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('branch')->nullable();
            $table->decimal('opening_balance', 14, 2)->default(0);
            $table->decimal('current_balance', 14, 2)->default(0);
            $table->string('currency', 5)->default('KES');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('cash_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('txn_no')->unique();
            $table->foreignId('bank_account_id')->constrained('bank_accounts');
            $table->date('txn_date');
            $table->string('type'); // INFLOW (Receipt), OUTFLOW (Disbursement), CONTRA (Transfer)
            $table->string('category'); // Sales Receipt, Petty Cash Voucher, Utilities, Fuel, Staff Float, Supplier Payment, Bank Deposit
            $table->decimal('amount', 14, 2);
            $table->string('payee_or_payer');
            $table->string('reference_no')->nullable();
            $table->text('description')->nullable();
            $table->string('approved_by')->default('Finance Manager');
            $table->boolean('is_reconciled')->default(false);
            $table->timestamps();
        });

        Schema::create('mpesa_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('mpesa_receipt_no')->unique(); // e.g. QAL8392019
            $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts')->nullOnDelete();
            $table->string('type')->default('Paybill'); // Paybill, Till (Buy Goods), B2C Disbursement
            $table->string('shortcode'); // e.g. 400200 or 543210
            $table->string('phone_number');
            $table->string('customer_name');
            $table->decimal('amount', 12, 2);
            $table->dateTime('transaction_time');
            $table->string('account_reference')->nullable(); // e.g. INV-001 or Customer ID
            $table->string('status')->default('Allocated'); // Unallocated, Allocated, Reconciled
            $table->string('allocated_to_type')->nullable(); // SalesInvoice, Customer, POS
            $table->unsignedBigInteger('allocated_to_id')->nullable();
            $table->timestamps();
        });

        Schema::create('bank_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->string('reconciliation_no')->unique();
            $table->foreignId('bank_account_id')->constrained('bank_accounts');
            $table->date('statement_date');
            $table->decimal('statement_ending_balance', 14, 2);
            $table->decimal('book_balance', 14, 2);
            $table->decimal('cleared_balance', 14, 2)->default(0);
            $table->decimal('difference', 14, 2)->default(0);
            $table->string('status')->default('Reconciled'); // Draft, In Progress, Reconciled
            $table->string('reconciled_by')->default('Finance Manager');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('bank_statement_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_reconciliation_id')->constrained('bank_reconciliations')->cascadeOnDelete();
            $table->date('line_date');
            $table->string('description');
            $table->string('reference')->nullable();
            $table->decimal('withdrawal', 12, 2)->default(0);
            $table->decimal('deposit', 12, 2)->default(0);
            $table->decimal('running_balance', 14, 2)->default(0);
            $table->boolean('is_matched')->default(true);
            $table->string('matched_ref')->nullable();
            $table->timestamps();
        });

        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('type'); // Asset, Liability, Equity, Revenue, Expense
            $table->string('sub_category'); // Current Asset, Fixed Asset, Current Liability, Operating Revenue, Direct Cost, Admin Expense
            $table->string('normal_balance')->default('Debit'); // Debit, Credit
            $table->decimal('opening_balance', 14, 2)->default(0);
            $table->decimal('current_balance', 14, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->string('entry_no')->unique();
            $table->date('entry_date');
            $table->string('reference_type')->nullable(); // SalesInvoice, PurchaseInvoice, Receipt, PaymentVoucher, Manual
            $table->string('reference_no')->nullable();
            $table->text('description');
            $table->decimal('total_debit', 14, 2);
            $table->decimal('total_credit', 14, 2);
            $table->string('posted_by')->default('Finance Manager');
            $table->timestamps();
        });

        Schema::create('journal_entry_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained('journal_entries')->cascadeOnDelete();
            $table->foreignId('account_id')->constrained('chart_of_accounts');
            $table->decimal('debit', 14, 2)->default(0);
            $table->decimal('credit', 14, 2)->default(0);
            $table->string('narration')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entry_lines');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('chart_of_accounts');
        Schema::dropIfExists('bank_statement_lines');
        Schema::dropIfExists('bank_reconciliations');
        Schema::dropIfExists('mpesa_transactions');
        Schema::dropIfExists('cash_transactions');
        Schema::dropIfExists('bank_accounts');
    }
};
