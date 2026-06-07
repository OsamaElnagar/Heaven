<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\MaritalStatus;
use App\Models\Concerns\HasEntityCode;
use App\Observers\PartyAccountObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

#[ObservedBy([PartyAccountObserver::class])]
class Client extends Model
{
    use HasEntityCode;
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'name_en',
        'national_id',
        'passport_number',
        'passport_expiry',
        'phone',
        'phone_alt',
        'email',
        'gender',
        'marital_status',
        'date_of_birth',
        'governorate',
        'address',
        'mahram_name',
        'mahram_relation',
        'mahram_phone',
        'blood_type',
        'medical_notes',
        'account_id',
    ];

    protected $casts = [
        'gender' => Gender::class,
        'marital_status' => MaritalStatus::class,
        'passport_expiry' => 'date',
        'date_of_birth' => 'date',
    ];

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function visas(): HasManyThrough
    {
        return $this->hasManyThrough(Visa::class, Booking::class);
    }

    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(ReceiptVoucher::class, Booking::class, 'client_id', 'booking_id');
    }

    public function payouts(): HasManyThrough
    {
        return $this->hasManyThrough(RefundVoucher::class, Booking::class, 'client_id', 'booking_id');
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
        return 'CLI';
    }
}
