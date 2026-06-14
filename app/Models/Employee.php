<?php

namespace App\Models;

use App\Enums\EmployeeType;
use App\Enums\SalaryType;
use App\Models\Concerns\HasEntityCode;
use App\Observers\PartyAccountObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy([PartyAccountObserver::class])]
class Employee extends Model
{
    use HasEntityCode;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'code',
        'name',
        'national_id',
        'phone',
        'email',
        'address',
        'role',
        'job_title',
        'daily_hours',
        'type',
        'salary_type',
        'base_salary',
        'hire_date',
        'termination_date',
        'is_active',
        'account_id',
        'advance_account_id',
        'department_id',
        'notes',
    ];

    protected $casts = [
        'type' => EmployeeType::class,
        'salary_type' => SalaryType::class,
        'hire_date' => 'date',
        'termination_date' => 'date',
        'is_active' => 'boolean',
        'daily_hours' => 'decimal:2',
        'base_salary' => 'integer',
    ];

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function advanceAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'advance_account_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function journalLines(): HasMany
    {
        return $this->hasMany(JournalLine::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function employeeAdvances(): HasMany
    {
        return $this->hasMany(EmployeeAdvance::class);
    }

    public function payrollLines(): HasMany
    {
        return $this->hasMany(PayrollLine::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public static function entityCodeType(): string
    {
        return 'EMP';
    }
}
