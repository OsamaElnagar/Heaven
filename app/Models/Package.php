<?php

namespace App\Models;

use App\Enums\PackageGrade;
use App\Observers\PackageObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[ObservedBy([PackageObserver::class])]
class Package extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
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
        'front_office_visible',
    ];

    protected $casts = [
        'grade' => PackageGrade::class,
        'departure_date' => 'date',
        'return_date' => 'date',
        'is_active' => 'boolean',
        'front_office_visible' => 'boolean',
    ];

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public static function booted(): void
    {
        static::creating(function (Package $package) {
            if (empty($package->slug)) {
                $package->slug = Str::slug($package->name).'-'.Str::random(6);
            }
        });
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
