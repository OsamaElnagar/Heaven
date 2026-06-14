<?php

namespace App\Models;

use App\Enums\EmployeeAdvanceStatus;
use App\Enums\EmployeeAdvanceType;
use App\Models\Concerns\HasEntityCode;
use App\Observers\EmployeeAdvanceObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([EmployeeAdvanceObserver::class])]
class EmployeeAdvance extends Model
{
    use HasEntityCode;

    protected $fillable = [
        'code',
        'employee_id',
        'advance_date',
        'amount',
        'repaid_amount',
        'installments',
        'type',
        'status',
        'safe_id',
        'journal_entry_id',
        'notes',
    ];

    protected $casts = [
        'type' => EmployeeAdvanceType::class,
        'status' => EmployeeAdvanceStatus::class,
        'advance_date' => 'date',
        'amount' => 'integer',
        'repaid_amount' => 'integer',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function safe(): BelongsTo
    {
        return $this->belongsTo(Safe::class);
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function getRemainingAmount(): int
    {
        return $this->amount - $this->repaid_amount;
    }

    public function isFullyRepaid(): bool
    {
        return $this->status === 'fully_repaid';
    }

    public static function entityCodeType(): string
    {
        return 'EA';
    }
}
