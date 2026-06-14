<?php

namespace Database\Seeders;

use App\Enums\EmployeeType;
use App\Models\Employee;
use App\Models\FiscalYear;
use App\Models\PayrollLine;
use App\Models\PayrollRun;
use App\Services\PayrollService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PayrollRunSeeder extends Seeder
{
    public function run(): void
    {
        $fiscalYear = FiscalYear::first();
        $employees = Employee::where('is_active', true)->get();

        if (! $fiscalYear || $employees->isEmpty()) {
            return;
        }

        $activeEmployees = $employees->filter(fn ($e) => $e->type !== EmployeeType::DAILY);

        if ($activeEmployees->isEmpty()) {
            return;
        }

        $months = [1, 2, 3, 4, 5, 6];

        foreach ($months as $month) {
            $this->generateMonthlyRun($month, 2025, $fiscalYear->id, $activeEmployees);
        }

        $dailyEmployees = $employees->filter(fn ($e) => $e->type === EmployeeType::DAILY);

        if ($dailyEmployees->isNotEmpty()) {
            $this->generateDailyRun(1, 2025, $fiscalYear->id, $dailyEmployees);
        }
    }

    private function generateMonthlyRun(int $month, int $year, int $fiscalYearId, $employees): void
    {
        $run = PayrollRun::firstOrCreate(
            ['month' => $month, 'year' => $year, 'type' => 'monthly'],
            [
                'fiscal_year_id' => $fiscalYearId,
                'created_by' => 1,
                'status' => 'draft',
            ]
        );

        if ($run->lines()->exists()) {
            return;
        }

        $daysInMonth = Carbon::create($year, $month)->daysInMonth;

        foreach ($employees as $employee) {
            $attendanceDays = rand(20, $daysInMonth);
            $absenceDays = $daysInMonth - $attendanceDays;

            $overtimeHours = rand(0, 10);

            $dailyRate = ($employee->base_salary ?? 0) / max($daysInMonth, 1);
            $hourlyRate = ($employee->daily_hours ?? 8) > 0 ? $dailyRate / ($employee->daily_hours ?? 8) : 0;
            $overtimeValue = (int) round($overtimeHours * $hourlyRate * 1.5);

            $proratedBase = (int) round(($employee->base_salary ?? 0) * $attendanceDays / $daysInMonth);

            $gross = $proratedBase + $overtimeValue;
            $socialInsurance = (int) round($gross * 0.11);
            $tax = $this->calculateTax($gross - $socialInsurance);
            $net = max(0, $gross - $socialInsurance - $tax);

            PayrollLine::create([
                'payroll_run_id' => $run->id,
                'employee_id' => $employee->id,
                'days_in_month' => $daysInMonth,
                'attendance_days' => $attendanceDays,
                'absence_days' => $absenceDays,
                'base_salary' => $proratedBase,
                'overtime_hours' => $overtimeHours,
                'overtime' => $overtimeValue,
                'gross_salary' => $gross,
                'social_insurance' => $socialInsurance,
                'income_tax' => $tax,
                'net_salary' => $net,
                'paid_amount' => ($month <= 3) ? $net : 0,
                'remaining_amount' => ($month <= 3) ? 0 : $net,
                'is_paid' => ($month <= 3),
            ]);
        }

        app(PayrollService::class)->updateTotals($run);
    }

    private function generateDailyRun(int $month, int $year, int $fiscalYearId, $employees): void
    {
        $run = PayrollRun::firstOrCreate(
            ['month' => $month, 'year' => $year, 'type' => 'daily'],
            [
                'fiscal_year_id' => $fiscalYearId,
                'created_by' => 1,
                'status' => 'draft',
            ]
        );

        if ($run->lines()->exists()) {
            return;
        }

        foreach ($employees as $employee) {
            $attendanceDays = rand(15, 26);
            $base = $employee->base_salary ?? 200;
            $gross = $base * $attendanceDays;
            $net = max(0, $gross);

            PayrollLine::create([
                'payroll_run_id' => $run->id,
                'employee_id' => $employee->id,
                'days_in_month' => Carbon::create($year, $month)->daysInMonth,
                'attendance_days' => $attendanceDays,
                'base_salary' => $gross,
                'gross_salary' => $gross,
                'net_salary' => $net,
                'paid_amount' => $net,
                'remaining_amount' => 0,
                'is_paid' => true,
            ]);
        }

        app(PayrollService::class)->updateTotals($run);
    }

    private function calculateTax(int $taxableIncome): int
    {
        return match (true) {
            $taxableIncome <= 1250 => 0,
            $taxableIncome <= 2500 => (int) round(($taxableIncome - 1250) * 0.02),
            $taxableIncome <= 3750 => 25 + (int) round(($taxableIncome - 2500) * 0.05),
            $taxableIncome <= 5000 => 88 + (int) round(($taxableIncome - 3750) * 0.10),
            $taxableIncome <= 16667 => 213 + (int) round(($taxableIncome - 5000) * 0.15),
            default => 1963 + (int) round(($taxableIncome - 16667) * 0.20),
        };
    }
}
