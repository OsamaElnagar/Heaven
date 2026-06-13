<?php

namespace App\Models;

use App\Enums\BookingChannel;
use App\Enums\BookingStatus;
use App\Enums\RoomType;
use App\Observers\BookingObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy([BookingObserver::class])]
class Booking extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'reference',
        'client_id',
        'package_id',
        'trip_id',
        'room_id',
        'status',
        'room_type',
        'channel',
        'branch_id',
        'agent_id',
        'total_price',
        'discount',
        'net_price',
        'paid_amount',
        'due_date',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'status' => BookingStatus::class,
        'room_type' => RoomType::class,
        'channel' => BookingChannel::class,
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function receiptVouchers(): HasMany
    {
        return $this->hasMany(ReceiptVoucher::class);
    }

    public function refundVouchers(): HasMany
    {
        return $this->hasMany(RefundVoucher::class);
    }

    public function visa(): HasOne
    {
        return $this->hasOne(Visa::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class);
    }
}
