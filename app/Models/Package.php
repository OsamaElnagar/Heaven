<?php

namespace App\Models;

use App\Enums\PackageGrade;
use App\Enums\PackageType;
use App\Observers\PackageObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy([PackageObserver::class])]
class Package extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'grade',
        'season_year',
        'duration_nights',
        'base_price',
        'total_seats',
        'reserved_seats',
        'departure_date',
        'return_date',
        'includes',
        'excludes',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'type' => PackageType::class,
        'grade' => PackageGrade::class,
        'departure_date' => 'date',
        'return_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function hotels(): BelongsToMany
    {
        return $this->belongsToMany(Hotel::class, 'package_hotels')
            ->using(PackageHotel::class)
            ->withPivot(['city', 'nights', 'cost_per_person'])
            ->withTimestamps();
    }
}
