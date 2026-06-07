<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountOpeningBalance extends Model
{
    protected $fillable = [
        'account_id',
        'fiscal_year_id',
        'debit_amount',
        'credit_amount',
    ];

    protected $casts = [
        'debit_amount' => 'integer',
        'credit_amount' => 'integer',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function getNetAmount(): int
    {
        return $this->debit_amount - $this->credit_amount;
    }
}
