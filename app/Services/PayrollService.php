<?php

namespace App\Services;

use App\Enums\EmployeeType;
use App\Enums\SalaryType;
use App\Models\Employee;
use App\Models\FiscalYear;
use App\Models\PayrollLine;
use App\Models\PayrollRun;
use App\Services\Accounting\DocumentSequenceService;
use Illuminate\Support\Collection;

class PayrollService
{
    public function createRun(array $data): PayrollRun
    {
        $fiscalYear = FiscalYear::findOrFail($data['fiscal_year_id']);

        if ($fiscalYear->status !== 'open') {
            throw new \InvalidArgumentException('Fiscal year must be open');
        }

        return PayrollRun::create([
            'code' => $this->generateNumber($fiscalYear->id),
            'fiscal_year_id' => $fiscalYear->id,
            'month' => $data['month'],
            'year' => $data['year'],
            'type' => $data['type'] ?? 'monthly',
            'created_by' => $data['created_by'],
            'status' => 'draft',
        ]);
    }

    public function addEmployee(PayrollRun $run, int $employeeId): PayrollLine
    {
        $employee = Employee::findOrFail($employeeId);

        if (! $employee->is_active) {
            throw new \InvalidArgumentException("Employee {$employee->name} is not active");
        }

        $calculation = $this->calculateLine($employee, $run);

        return PayrollLine::create(array_merge($calculation, [
            'payroll_run_id' => $run->id,
            'employee_id' => $employeeId,
            'is_paid' => false,
        ]));
    }

    public function addAllEmployees(PayrollRun $run): Collection
    {
        $query = Employee::where('is_active', true);

        if ($run->type === 'daily') {
            $query->where('type', 'daily');
        }

        $employees = $query->get();

        $lines = [];

        foreach ($employees as $employee) {
            $lines[] = $this->addEmployee($run, $employee->id);
        }

        return collect($lines);
    }

    public function generateLines(PayrollRun $run): int
    {
        if ($run->lines()->exists()) {
            throw new \InvalidArgumentException('Payroll run already has lines. Delete them first.');
        }

        $query = Employee::where('is_active', true);

        if ($run->type === 'daily') {
            $query->where('type', 'daily');
        }

        $employees = $query->get();

        if ($employees->isEmpty()) {
            return 0;
        }

        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $run->month, $run->year);
        $generatedCount = 0;

        foreach ($employees as $employee) {
            $attendances = $employee->attendances()
                ->whereMonth('date', $run->month)
                ->whereYear('date', $run->year)
                ->get();

            $halfDayCount = $attendances->where('status', 'half_day')->count();
            $attendanceDays = $attendances->whereIn('status', ['present', 'late', 'overtime'])->count() + $halfDayCount;
            $absenceDays = $attendances->where('status', 'absent')->count();
            $weekendHolidayDays = $attendances->whereIn('status', ['weekend', 'holiday'])->count();
            $totalOvertimeHours = (float) $attendances->sum('overtime_hours');

            $dailyHours = (float) ($employee->daily_hours ?? 8);

            $workingDaysInMonth = $daysInMonth - $weekendHolidayDays;
            $workingDaysInMonth = max($workingDaysInMonth, 1);

            $effectiveAttendanceDays = $attendanceDays - ($halfDayCount * 0.5);

            $salaryType = $employee->salary_type;

            if ($salaryType === SalaryType::HOURLY) {
                $totalHoursWorked = $attendanceDays * $dailyHours + $totalOvertimeHours;
                $expectedMonthlyHours = $daysInMonth * $dailyHours;
                $proratedBaseSalary = $expectedMonthlyHours > 0
                    ? (int) round(($employee->base_salary ?? 0) * $totalHoursWorked / $expectedMonthlyHours)
                    : 0;
            } else {
                $proratedBaseSalary = $workingDaysInMonth > 0
                    ? (int) round(($employee->base_salary ?? 0) * $effectiveAttendanceDays / $workingDaysInMonth)
                    : 0;
            }

            $dailyRate = $workingDaysInMonth > 0
                ? ($employee->base_salary ?? 0) / $workingDaysInMonth
                : 0;

            $hourlyRate = $dailyHours > 0 ? $dailyRate / $dailyHours : 0;

            $overtimeValue = (int) round($totalOvertimeHours * $hourlyRate * 1.5);

            $calculation = $this->calculateLine(
                $employee,
                $run,
                $proratedBaseSalary,
                $overtimeValue,
                $attendanceDays,
                $absenceDays,
                $daysInMonth,
                $totalOvertimeHours,
            );

            PayrollLine::create(array_merge($calculation, [
                'payroll_run_id' => $run->id,
                'employee_id' => $employee->id,
                'is_paid' => false,
            ]));

            $generatedCount++;
        }

        return $generatedCount;
    }

    public function calculateLine(
        Employee $employee,
        PayrollRun $run,
        ?int $baseSalaryOverride = null,
        ?int $overtimeOverride = null,
        ?int $attendanceDays = null,
        ?int $absenceDays = null,
        ?int $daysInMonth = null,
        ?float $overtimeHours = null,
    ): array {
        $baseSalary = $baseSalaryOverride ?? $employee->base_salary;
        $allowances = $this->calculateAllowances($employee, $run->type);
        $overtime = $overtimeOverride ?? 0;
        $bonuses = 0;

        $grossSalary = $baseSalary + $allowances + $overtime + $bonuses;

        $socialInsurance = $this->calculateSocialInsurance($grossSalary, $employee->type);
        $incomeTax = $this->calculateIncomeTax($grossSalary, $socialInsurance);
        $advancesDeduction = $this->getActiveAdvancesDeduction($employee, $run->type);
        $otherDeductions = 0;

        $netSalary = max(0, $grossSalary - $socialInsurance - $incomeTax - $advancesDeduction - $otherDeductions);

        $result = [
            'base_salary' => $baseSalary,
            'allowances' => $allowances,
            'overtime' => $overtime,
            'bonuses' => $bonuses,
            'gross_salary' => $grossSalary,
            'social_insurance' => $socialInsurance,
            'income_tax' => $incomeTax,
            'advances_deduction' => $advancesDeduction,
            'other_deductions' => $otherDeductions,
            'net_salary' => $netSalary,
            'paid_amount' => 0,
            'remaining_amount' => $netSalary,
        ];

        if ($attendanceDays !== null) {
            $result['attendance_days'] = $attendanceDays;
        }

        if ($absenceDays !== null) {
            $result['absence_days'] = $absenceDays;
        }

        if ($daysInMonth !== null) {
            $result['days_in_month'] = $daysInMonth;
        }

        if ($overtimeHours !== null) {
            $result['overtime_hours'] = $overtimeHours;
        }

        return $result;
    }

    public function calculateTotals(PayrollRun $run): array
    {
        $lines = $run->lines();

        $totalGross = $lines->sum('gross_salary');
        $totalDeductions = $lines->sum('social_insurance') +
            $lines->sum('income_tax') +
            $lines->sum('advances_deduction') +
            $lines->sum('other_deductions');
        $totalNet = $lines->sum('net_salary');

        return [
            'total_gross' => $totalGross,
            'total_deductions' => $totalDeductions,
            'total_net' => $totalNet,
        ];
    }

    public function updateTotals(PayrollRun $run): PayrollRun
    {
        $totals = $this->calculateTotals($run);

        $run->update([
            'total_gross' => $totals['total_gross'],
            'total_deductions' => $totals['total_deductions'],
            'total_net' => $totals['total_net'],
        ]);

        return $run->fresh();
    }

    public function approve(PayrollRun $run): PayrollRun
    {
        if ($run->status !== 'draft') {
            throw new \InvalidArgumentException('Only draft payroll runs can be approved');
        }

        if (! $run->lines()->exists()) {
            throw new \InvalidArgumentException('Payroll run must have at least one employee');
        }

        $run->update(['status' => 'approved']);

        return $run->fresh();
    }

    public function post(PayrollRun $run, int $postedById): PayrollRun
    {
        if ($run->status !== 'approved') {
            throw new \InvalidArgumentException('Only approved payroll runs can be posted');
        }

        $journalEntry = app(JournalService::class)->post(
            'salary',
            $run->id,
            $this->getJournalLines($run),
            'Payroll '.$run->number,
        );

        $run->update([
            'status' => 'posted',
            'journal_entry_id' => $journalEntry->id,
        ]);

        return $run->fresh();
    }

    public function markAsPaid(PayrollRun $run, ?int $safeId = null): PayrollRun
    {
        if ($run->status !== 'posted') {
            throw new \InvalidArgumentException('Only posted payroll runs can be marked as paid');
        }

        $run->lines()->update(['is_paid' => true]);

        if ($safeId) {
            $run->lines()->update(['safe_id' => $safeId]);
        }

        $run->update(['status' => 'paid']);

        return $run->fresh();
    }

    public function calculateAllowances(Employee $employee, string $payType): int
    {
        return 0;
    }

    public function calculateSocialInsurance(int $grossSalary, EmployeeType $employeeType): int
    {
        if ($employeeType === EmployeeType::CONTRACTED) {
            return 0;
        }

        $rate = 0.11;

        return (int) round($grossSalary * $rate);
    }

    public function calculateIncomeTax(int $grossSalary, int $socialInsurance): int
    {
        $taxableIncome = $grossSalary - $socialInsurance;

        $monthlyTax = match (true) {
            $taxableIncome <= 1250 => 0,
            $taxableIncome <= 2500 => (int) round(($taxableIncome - 1250) * 0.02),
            $taxableIncome <= 3750 => 25 + (int) round(($taxableIncome - 2500) * 0.05),
            $taxableIncome <= 5000 => 88 + (int) round(($taxableIncome - 3750) * 0.10),
            $taxableIncome <= 16667 => 213 + (int) round(($taxableIncome - 5000) * 0.15),
            default => 1963 + (int) round(($taxableIncome - 16667) * 0.20),
        };

        return $monthlyTax;
    }

    protected function getActiveAdvancesDeduction(Employee $employee, ?string $runType = null): int
    {
        if ($runType !== null && $runType !== 'monthly') {
            return 0;
        }

        $totalDeduction = 0;

        $activeAdvances = $employee->employeeAdvances()
            ->where('status', 'active')
            ->get();

        foreach ($activeAdvances as $advance) {
            $outstanding = $advance->amount - $advance->repaid_amount;

            if ($outstanding <= 0) {
                continue;
            }

            $installment = $advance->installments > 0
                ? (int) floor($advance->amount / $advance->installments)
                : $outstanding;

            $totalDeduction += min($installment, $outstanding);
        }

        return $totalDeduction;
    }

    protected function getJournalLines(PayrollRun $run): array
    {
        $lines = [];

        $socialInsurancePayable = 0;
        $incomeTaxPayable = 0;

        foreach ($run->lines as $line) {
            if ($line->net_salary > 0) {
                $lines[] = [
                    'account_id' => $line->employee->account_id,
                    'debit_amount' => $line->net_salary,
                    'employee_id' => $line->employee_id,
                    'description' => 'راتب '.$line->employee->name,
                ];
            }

            $socialInsurancePayable += $line->social_insurance;
            $incomeTaxPayable += $line->income_tax;
        }

        $lines[] = [
            'account_id' => $this->getPayrollExpenseAccount(),
            'credit_amount' => $run->total_gross,
            'description' => 'مصروف رواتب - '.$run->number,
        ];

        return $lines;
    }

    protected function generateNumber(int $fiscalYearId): string
    {
        return app(DocumentSequenceService::class)
            ->getNextNumber('PA', $fiscalYearId);
    }

    protected function getSocialInsuranceAccount(): int
    {
        return 1;
    }

    protected function getIncomeTaxAccount(): int
    {
        return 1;
    }

    protected function getPayrollExpenseAccount(): int
    {
        return 1;
    }

    public function recordPayment(PayrollLine $line, int $amount, ?int $safeId = null): void
    {
        $safeId = $safeId ?? $line->safe_id;

        if (! $safeId) {
            throw new \InvalidArgumentException('يجب تحديد الخزينة');
        }

        $journalLines = [
            [
                'account_id' => $line->employee->account_id,
                'debit_amount' => $amount,
                'description' => 'صرف راتب '.$line->employee->name.' - '.$line->payrollRun->month.'/'.$line->payrollRun->year,
                'employee_id' => $line->employee_id,
            ],
            [
                'account_id' => $safeId,
                'credit_amount' => $amount,
                'description' => 'صرف من الخزينة - راتب '.$line->employee->name,
            ],
        ];

        app(JournalService::class)->post(
            'payroll_payment',
            $line->id,
            $journalLines,
            'صرف راتب '.$line->employee->name.' - '.$line->payrollRun->number,
        );
    }

    public function recordBulkPayment(Collection $lines, ?int $safeId = null): int
    {
        if ($lines->isEmpty()) {
            return 0;
        }

        $safeId = $safeId ?? $lines->first()->safe_id;

        if (! $safeId) {
            throw new \InvalidArgumentException('يجب تحديد الخزينة');
        }

        $journalLines = [];
        $totalAmount = 0;

        foreach ($lines as $line) {
            $amount = $line->remaining_amount > 0 ? $line->remaining_amount : $line->net_salary;
            $totalAmount += $amount;

            $journalLines[] = [
                'account_id' => $line->employee->account_id,
                'debit_amount' => $amount,
                'description' => 'صرف راتب '.$line->employee->name.' - '.$line->payrollRun->month.'/'.$line->payrollRun->year,
                'employee_id' => $line->employee_id,
            ];
        }

        $journalLines[] = [
            'account_id' => $safeId,
            'credit_amount' => $totalAmount,
            'description' => 'صرف من الخزينة - رواتب',
        ];

        $firstLine = $lines->first();
        $run = $firstLine->payrollRun;

        app(JournalService::class)->post(
            'payroll_bulk_payment',
            $run->id,
            $journalLines,
            'صرف رواتب - '.$run->number.' - '.count($lines).' موظف',
        );

        foreach ($lines as $line) {
            $line->safe_id = $safeId;
            $line->save();
        }

        return $totalAmount;
    }

    public function duplicateRun(PayrollRun $run, int $postedById): PayrollRun
    {
        $nextMonth = $run->month === 12 ? 1 : $run->month + 1;
        $nextYear = $run->month === 12 ? $run->year + 1 : $run->year;

        $existing = PayrollRun::where('month', $nextMonth)
            ->where('year', $nextYear)
            ->where('type', $run->type)
            ->first();

        if ($existing) {
            throw new \InvalidArgumentException('يوجد مسير بالفعل لهذا الشهر والنوع');
        }

        $newRun = PayrollRun::create([
            'fiscal_year_id' => $run->fiscal_year_id,
            'month' => $nextMonth,
            'year' => $nextYear,
            'type' => $run->type,
            'created_by' => $postedById,
            'status' => 'draft',
        ]);

        return $newRun;
    }

    public function getSummary(?int $fiscalYearId = null): array
    {
        $query = PayrollRun::query();

        if ($fiscalYearId) {
            $query->where('fiscal_year_id', $fiscalYearId);
        }

        $runs = $query->withCount('lines')->get();

        $totalRuns = $runs->count();
        $totalLines = $runs->sum('lines_count');
        $totalGross = $runs->sum('total_gross');
        $totalDeductions = $runs->sum('total_deductions');
        $totalNet = $runs->sum('total_net');
        $totalPaid = PayrollLine::whereIn('payroll_run_id', $runs->pluck('id'))->sum('paid_amount');

        return [
            'total_runs' => $totalRuns,
            'total_lines' => $totalLines,
            'total_gross' => $totalGross,
            'total_deductions' => $totalDeductions,
            'total_net' => $totalNet,
            'total_paid' => $totalPaid,
            'total_remaining' => $totalNet - $totalPaid,
        ];
    }
}
