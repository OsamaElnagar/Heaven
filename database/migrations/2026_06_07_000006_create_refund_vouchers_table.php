<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('refund_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('number', 30)->unique();
            $table->date('voucher_date');
            $table->string('payment_method', 10);
            $table->foreignId('safe_id')->nullable()->constrained('safes');
            $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts');
            $table->string('cheque_number')->nullable();
            $table->date('cheque_date')->nullable();
            $table->bigInteger('amount');

            $table->string('party_type', 20);
            $table->foreignId('client_id')->nullable()->constrained('clients');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers');

            $table->string('reference')->nullable();
            $table->text('description')->nullable();
            $table->string('attachment')->nullable();

            $table->string('status', 10)->default('draft');
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('posted_by')->nullable()->constrained('users');
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['voucher_date', 'status']);
            $table->index(['client_id', 'status']);
            $table->index(['supplier_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refund_vouchers');
    }
};
