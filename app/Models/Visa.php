<?php

namespace App\Models;

use App\Enums\VisaStatus;
use App\Observers\VisaObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([VisaObserver::class])]
class Visa extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'status',
        'applied_at',
        'approved_at',
        'expiry_date',
        'visa_number',
        'rejection_reason',
        'notes',
    ];

    protected $casts = [
        'status' => VisaStatus::class,
        'applied_at' => 'date',
        'approved_at' => 'date',
        'expiry_date' => 'date',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
