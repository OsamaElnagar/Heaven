<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PackageHotel extends Pivot
{
    protected $table = 'package_hotels';

    protected $fillable = [
        'package_id',
        'hotel_id',
        'city',
        'nights',
        'cost_per_person',
    ];

    public $timestamps = true;

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }
}
