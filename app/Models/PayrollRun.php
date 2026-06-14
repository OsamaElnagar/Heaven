<?php

namespace App\Models;

use App\Enums\PayrollRunStatus;
use App\Enums\PayrollRunType;
use App\Models\Concerns\HasEntityCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollRun extends Model
{
    use HasEntityCode;

    protected $fillable = [
        'code',
        'fiscal_year_id',
        'month',
        'year',
        'type',
        'total_gross',
        'total_deductions',
        'total_net',
        'status',
        'journal_entry_id',
        'created_by',
    ];

    protected $casts = [
        'type' => PayrollRunType::class,
        'status' => PayrollRunStatus::class,
        'total_gross' => 'integer',
        'total_deductions' => 'integer',
        'total_net' => 'integer',
    ];

    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(PayrollLine::class);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopePosted($query)
    {
        return $query->where('status', 'posted');
    }

    public static function entityCodeType(): string
    {
        return 'PA';
    }
}
