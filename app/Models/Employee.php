<?php

namespace App\Models;

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
        'role',
        'salary_type',
        'salary',
        'hired_at',
        'left_at',
        'is_active',
        'account_id',
    ];

    protected $casts = [
        'salary_type' => SalaryType::class,
        'hired_at' => 'date',
        'left_at' => 'date',
        'is_active' => 'boolean',
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

    public function journalLines(): HasMany
    {
        return $this->hasMany(JournalLine::class);
    }

    public static function entityCodeType(): string
    {
        return 'EMP';
    }
}
