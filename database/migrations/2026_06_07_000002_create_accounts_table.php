<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('class', 20);
            $table->string('type', 20)->default('detail');
            $table->string('normal_balance', 20);
            $table->foreignId('parent_id')->nullable()->constrained('accounts');
            $table->integer('level')->default(1);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('account_opening_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('accounts');
            $table->foreignId('fiscal_year_id')->constrained('fiscal_years');
            $table->bigInteger('debit_amount')->default(0);
            $table->bigInteger('credit_amount')->default(0);
            $table->timestamps();
            $table->unique(['account_id', 'fiscal_year_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_opening_balances');
        Schema::dropIfExists('accounts');
    }
};
