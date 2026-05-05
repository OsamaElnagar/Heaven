<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\MaritalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
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
    ];

    protected $casts = [
        'gender' => Gender::class,
        'marital_status' => MaritalStatus::class,
        'passport_expiry' => 'date',
        'date_of_birth' => 'date',
    ];

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
        return $this->hasManyThrough(Payment::class, Booking::class);
    }
}
