<?php

namespace Database\Seeders;

use App\Models\Package;
use App\Models\Trip;
use Illuminate\Database\Seeder;

class TripSeeder extends Seeder
{
    public function run(): void
    {
        if (Trip::count() > 0) {
            return;
        }

        $packages = Package::all();

        foreach ($packages as $package) {
            $count = fake()->numberBetween(1, 2);

            for ($i = 0; $i < $count; $i++) {
                Trip::factory()->create([
                    'package_id' => $package->id,
                    'departure_at' => $package->departure_date,
                    'return_at' => $package->return_date,
                ]);
            }
        }
    }
}
