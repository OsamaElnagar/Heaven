<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_runs', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->nullable()->unique();
            $table->foreignId('fiscal_year_id')->constrained()->cascadeOnDelete();
            $table->integer('month');
            $table->integer('year');
            $table->string('type')->default('monthly');
            $table->integer('total_gross')->default(0);
            $table->integer('total_deductions')->default(0);
            $table->integer('total_net')->default(0);
            $table->string('status')->default('draft');
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });

        Schema::create('payroll_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_run_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->integer('days_in_month')->default(30);
            $table->integer('attendance_days')->default(0);
            $table->integer('absence_days')->default(0);
            $table->integer('base_salary')->default(0);
            $table->integer('allowances')->default(0);
            $table->integer('overtime')->default(0);
            $table->decimal('overtime_hours', 8, 2)->default(0);
            $table->integer('bonuses')->default(0);
            $table->integer('gross_salary')->default(0);
            $table->integer('social_insurance')->default(0);
            $table->integer('income_tax')->default(0);
            $table->integer('advances_deduction')->default(0);
            $table->integer('other_deductions')->default(0);
            $table->integer('net_salary')->default(0);
            $table->integer('paid_amount')->default(0);
            $table->integer('remaining_amount')->default(0);
            $table->boolean('is_paid')->default(false);
            $table->foreignId('safe_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_lines');
        Schema::dropIfExists('payroll_runs');
    }
};
