<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentSequence extends Model
{
    protected $fillable = [
        'document_type',
        'fiscal_year_id',
        'prefix',
        'last_number',
        'padding',
    ];

    protected $casts = [
        'last_number' => 'integer',
        'padding' => 'integer',
    ];

    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function formatNumber(int $number): string
    {
        return $this->prefix.str_pad($number, $this->padding, '0', STR_PAD_LEFT);
    }
}
