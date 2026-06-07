<?php

namespace App\Models;

use App\Enums\JournalEntrySourceType;
use App\Enums\JournalEntryStatus;
use App\Observers\JournalEntryObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy([JournalEntryObserver::class])]
class JournalEntry extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'number',
        'fiscal_year_id',
        'entry_date',
        'status',
        'source_type',
        'source_id',
        'description',
        'reference',
        'notes',
        'attachment',
        'reversed_by_entry_id',
        'reversal_of_entry_id',
        'created_by',
        'posted_by',
        'posted_at',
    ];

    protected $casts = [
        'status' => JournalEntryStatus::class,
        'source_type' => JournalEntrySourceType::class,
        'entry_date' => 'date',
        'posted_at' => 'datetime',
    ];

    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(JournalLine::class);
    }

    public function reversedByEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'reversed_by_entry_id');
    }

    public function reversalOfEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'reversal_of_entry_id');
    }

    public function refundVouchers(): HasMany
    {
        return $this->hasMany(RefundVoucher::class);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', JournalEntryStatus::DRAFT);
    }

    public function scopePosted($query)
    {
        return $query->where('status', JournalEntryStatus::POSTED);
    }

    public function scopeReversed($query)
    {
        return $query->where('status', JournalEntryStatus::REVERSED);
    }

    public function isBalanced(): bool
    {
        return $this->totalDebits() === $this->totalCredits();
    }

    public function totalDebits(): int
    {
        return (int) $this->lines()->sum('debit_amount');
    }

    public function totalCredits(): int
    {
        return (int) $this->lines()->sum('credit_amount');
    }
}
