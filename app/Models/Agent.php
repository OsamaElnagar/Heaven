<?php

namespace App\Models;

use App\Models\Concerns\HasEntityCode;
use App\Observers\PartyAccountObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy([PartyAccountObserver::class])]
class Agent extends Model
{
    use HasEntityCode;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'phone',
        'email',
        'national_id',
        'commission_percentage',
        'contract_date',
        'notes',
        'is_active',
        'account_id',
    ];

    protected $casts = [
        'commission_percentage' => 'decimal:2',
        'contract_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class);
    }

    public static function entityCodeType(): string
    {
        return 'AGT';
    }
}
