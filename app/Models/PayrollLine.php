<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollLine extends Model
{
    protected $fillable = [
        'payroll_run_id',
        'employee_id',
        'days_in_month',
        'attendance_days',
        'absence_days',
        'base_salary',
        'allowances',
        'overtime',
        'overtime_hours',
        'bonuses',
        'gross_salary',
        'social_insurance',
        'income_tax',
        'advances_deduction',
        'other_deductions',
        'net_salary',
        'paid_amount',
        'remaining_amount',
        'is_paid',
        'safe_id',
    ];

    protected $casts = [
        'days_in_month' => 'integer',
        'attendance_days' => 'integer',
        'absence_days' => 'integer',
        'base_salary' => 'integer',
        'allowances' => 'integer',
        'overtime' => 'integer',
        'overtime_hours' => 'decimal:2',
        'bonuses' => 'integer',
        'gross_salary' => 'integer',
        'social_insurance' => 'integer',
        'income_tax' => 'integer',
        'advances_deduction' => 'integer',
        'other_deductions' => 'integer',
        'net_salary' => 'integer',
        'paid_amount' => 'integer',
        'remaining_amount' => 'integer',
        'is_paid' => 'boolean',
    ];

    public function payrollRun(): BelongsTo
    {
        return $this->belongsTo(PayrollRun::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function safe(): BelongsTo
    {
        return $this->belongsTo(Safe::class);
    }
}
