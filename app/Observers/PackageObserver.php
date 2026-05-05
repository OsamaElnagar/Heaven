<?php

namespace App\Observers;

use App\Enums\PackageType;
use App\Models\Package;

class PackageObserver
{
    /**
     * Handle the Package "saving" event.
     */
    public function saving(Package $package): void
    {
        if ($package->reserved_seats > $package->total_seats) {
            $package->reserved_seats = $package->total_seats;
        }

        if (
            $package->type === PackageType::HAJJ
            && (int) $package->season_year < (int) now()->year
        ) {
            $package->is_active = false;
        }
    }
}
