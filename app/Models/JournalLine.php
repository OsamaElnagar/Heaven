<?php

namespace App\Models;

use App\Observers\JournalLineObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([JournalLineObserver::class])]
class JournalLine extends Model
{
    protected $fillable = [
        'journal_entry_id',
        'account_id',
        'debit_amount',
        'credit_amount',
        'description',
        'sort_order',
        'client_id',
        'supplier_id',
        'employee_id',
        'safe_id',
        'bank_account_id',
    ];

    protected $casts = [
        'debit_amount' => 'integer',
        'credit_amount' => 'integer',
    ];

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function safe(): BelongsTo
    {
        return $this->belongsTo(Safe::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function isDebit(): bool
    {
        return $this->debit_amount > 0;
    }

    public function isCredit(): bool
    {
        return $this->credit_amount > 0;
    }

    public function getTypeAttribute(): ?string
    {
        if ($this->debit_amount > 0) {
            return 'debit';
        }

        if ($this->credit_amount > 0) {
            return 'credit';
        }

        return null;
    }

    public function getAmountAttribute(): int
    {
        return $this->debit_amount > 0 ? $this->debit_amount : $this->credit_amount;
    }
}
