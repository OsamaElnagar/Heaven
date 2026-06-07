<?php

namespace App\Models;

use App\Enums\FiscalYearStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FiscalYear extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'starts_at',
        'ends_at',
        'status',
        'closed_at',
        'closed_by',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
        'closed_at' => 'datetime',
        'status' => FiscalYearStatus::class,
    ];

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class);
    }

    public function accountOpeningBalances(): HasMany
    {
        return $this->hasMany(AccountOpeningBalance::class);
    }

    public function documentSequences(): HasMany
    {
        return $this->hasMany(DocumentSequence::class);
    }

    public function isOpen(): bool
    {
        return $this->status === FiscalYearStatus::OPEN;
    }

    public function isClosed(): bool
    {
        return $this->status === FiscalYearStatus::CLOSED;
    }
}
