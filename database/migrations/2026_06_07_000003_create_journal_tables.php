<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->string('number', 30)->unique();
            $table->foreignId('fiscal_year_id')->constrained('fiscal_years');
            $table->date('entry_date');
            $table->string('status', 20)->default('draft');
            $table->string('source_type', 30)->default('manual');
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('description');
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->string('attachment')->nullable();
            $table->foreignId('reversed_by_entry_id')->nullable()->constrained('journal_entries');
            $table->foreignId('reversal_of_entry_id')->nullable()->constrained('journal_entries');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('posted_by')->nullable()->constrained('users');
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['entry_date', 'status']);
            $table->index(['source_type', 'source_id']);
            $table->index('fiscal_year_id');
        });

        Schema::create('journal_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained('journal_entries')->cascadeOnDelete();
            $table->foreignId('account_id')->constrained('accounts');
            $table->bigInteger('debit_amount')->default(0);
            $table->bigInteger('credit_amount')->default(0);
            $table->string('description')->nullable();
            $table->integer('sort_order')->default(0);

            $table->foreignId('client_id')->nullable()->constrained('clients');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers');
            $table->foreignId('employee_id')->nullable()->constrained('employees');
            $table->foreignId('safe_id')->nullable()->constrained('safes');
            $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts');

            $table->timestamps();

            $table->index('journal_entry_id');
            $table->index('account_id');
            $table->index('client_id');
            $table->index('supplier_id');
            $table->index('employee_id');
            $table->index('safe_id');
            $table->index('bank_account_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_lines');
        Schema::dropIfExists('journal_entries');
    }
};
