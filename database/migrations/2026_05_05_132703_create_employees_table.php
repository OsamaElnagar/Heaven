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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->nullable()->unique();
            $table->foreignId('account_id')->nullable()->constrained('accounts');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('advance_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->text('address')->nullable();
            $table->string('national_id')->unique();
            $table->string('role');                 // e.g. 'sales', 'operations', 'accountant', 'guide'
            $table->string('type')->default('permanent')->after('role');
            $table->string('job_title')->nullable()->after('role');
            $table->decimal('daily_hours', 8, 2)->default(8)->after('salary_type');
            $table->string('salary_type');         // SalaryType enum
            $table->decimal('base_salary', 10, 2);
            $table->date('hire_date');
            $table->date('termination_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
