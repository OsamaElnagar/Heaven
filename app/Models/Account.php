<?php

namespace App\Models;

use App\Enums\AccountClass;
use App\Enums\AccountNormalBalance;
use App\Enums\AccountType;
use App\Observers\AccountObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy([AccountObserver::class])]
class Account extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'name_en',
        'class',
        'type',
        'normal_balance',
        'parent_id',
        'level',
        'is_active',
        'is_system',
        'notes',
    ];

    protected $casts = [
        'class' => AccountClass::class,
        'type' => AccountType::class,
        'normal_balance' => AccountNormalBalance::class,
        'is_active' => 'boolean',
        'is_system' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    public function journalLines(): HasMany
    {
        return $this->hasMany(JournalLine::class);
    }

    public function accountOpeningBalances(): HasMany
    {
        return $this->hasMany(AccountOpeningBalance::class);
    }

    public function safes(): HasMany
    {
        return $this->hasMany(Safe::class);
    }

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(BankAccount::class);
    }

    public function isHeader(): bool
    {
        return $this->type === AccountType::HEADER;
    }

    public function isDetail(): bool
    {
        return $this->type === AccountType::DETAIL;
    }

    public function isDebitNormal(): bool
    {
        return $this->normal_balance === AccountNormalBalance::DEBIT;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeHeaders($query)
    {
        return $query->where('type', AccountType::HEADER);
    }

    public function scopeDetails($query)
    {
        return $query->where('type', AccountType::DETAIL);
    }

    public function scopeByClass($query, string $class)
    {
        return $query->where('class', $class);
    }
}
