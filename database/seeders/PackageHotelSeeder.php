<?php

namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageHotelSeeder extends Seeder
{
    public function run(): void
    {
        $packages = Package::all();
        $hotels = Hotel::all();

        if ($packages->isEmpty() || $hotels->isEmpty()) {
            return;
        }

        foreach ($packages as $package) {
            $selectedHotels = $hotels->random(fake()->numberBetween(1, min(3, $hotels->count())));

            foreach ($selectedHotels as $hotel) {
                $package->hotels()->syncWithoutDetaching([
                    $hotel->id => [
                        'city' => $hotel->city?->name ?? 'Unknown',
                        'nights' => fake()->numberBetween(3, 7),
                        'cost_per_person' => fake()->numberBetween(1000, 5000),
                    ],
                ]);
            }
        }
    }
}
