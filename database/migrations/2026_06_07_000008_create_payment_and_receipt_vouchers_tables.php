<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // -------------------------------------------------------
        // PAYMENT VOUCHERS - سندات الصرف
        // Paying money OUT from safe or bank to a payee
        // (supplier, employee, client refund, expense, or "other").
        // -------------------------------------------------------
        Schema::create('payment_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('number', 30)->unique();
            $table->date('voucher_date');
            $table->string('payment_method', 10)->default('safe');
            $table->foreignId('safe_id')->nullable()->constrained('safes');
            $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts');
            $table->string('cheque_number')->nullable();
            $table->date('cheque_date')->nullable();
            $table->bigInteger('amount');
            $table->bigInteger('withholding_amount')->default(0);
            $table->bigInteger('net_amount')->default(0);

            // Payee - exactly one of these is set, gated by payee_type
            $table->string('payee_type', 20);
            $table->foreignId('client_id')->nullable()->constrained('clients');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers');
            $table->foreignId('employee_id')->nullable()->constrained('employees');
            $table->string('payee_name')->nullable();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('agent_id')->nullable()->constrained()->nullOnDelete();

            // Optional link to a booking expense
            $table->foreignId('expense_id')->nullable()->constrained('expenses');

            $table->string('description');
            $table->string('reference')->nullable();
            $table->string('attachment')->nullable();
            $table->string('status', 10)->default('draft');

            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('posted_by')->nullable()->constrained('users');
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['voucher_date', 'status']);
        });

        // -------------------------------------------------------
        // RECEIPT VOUCHERS - سندات القبض
        // Receiving money IN to safe or bank from a payer
        // (client, supplier refund, employee, or "other").
        // -------------------------------------------------------
        Schema::create('receipt_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('number', 30)->unique();
            $table->date('voucher_date');
            $table->string('receipt_method', 10)->default('safe');
            $table->foreignId('safe_id')->nullable()->constrained('safes');
            $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts');
            $table->string('cheque_number')->nullable();
            $table->date('cheque_date')->nullable();
            $table->bigInteger('amount');
            $table->string('payment_type', 20)->nullable();

            // Payer - exactly one of these is set, gated by payer_type
            $table->string('payer_type', 20);
            $table->foreignId('client_id')->nullable()->constrained('clients');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers');
            $table->foreignId('employee_id')->nullable()->constrained('employees');
            $table->string('payer_name')->nullable();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();

            $table->string('description');
            $table->string('reference')->nullable();
            $table->string('attachment')->nullable();
            $table->string('status', 10)->default('draft');

            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('posted_by')->nullable()->constrained('users');
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['voucher_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipt_vouchers');
        Schema::dropIfExists('payment_vouchers');
    }
};
