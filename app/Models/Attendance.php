<?php

namespace App\Models;

use App\Enums\AttendanceStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use MassPrunable;

    protected $fillable = [
        'employee_id',
        'date',
        'check_in',
        'check_out',
        'status',
        'overtime_hours',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'status' => AttendanceStatus::class,
        'overtime_hours' => 'decimal:2',
    ];

    public function prunable(): Builder
    {
        return static::where('date', '<=', now()->subMonths(6));
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function scopeForMonth($query, int $month, int $year)
    {
        return $query->whereMonth('date', $month)->whereYear('date', $year);
    }

    public function scopePresent($query)
    {
        return $query->whereIn('status', [
            AttendanceStatus::PRESENT->value,
            AttendanceStatus::LATE->value,
            AttendanceStatus::HALF_DAY->value,
            AttendanceStatus::OVERTIME->value,
        ]);
    }

    public function scopeAbsent($query)
    {
        return $query->where('status', AttendanceStatus::ABSENT->value);
    }
}
