<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Hotel;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class HotelSeeder extends Seeder
{
    public function run(): void
    {
        if (Hotel::count() > 0) {
            return;
        }

        $makkah = City::where('name', 'Makkah')->first();
        $madinah = City::where('name', 'Madinah')->first();
        $suppliers = Supplier::all();

        if (! $makkah || ! $madinah || $suppliers->isEmpty()) {
            return;
        }

        for ($i = 0; $i < 5; $i++) {
            Hotel::factory()->forCity($makkah)->create([
                'supplier_id' => $suppliers->random()->id,
            ]);
        }

        for ($i = 0; $i < 3; $i++) {
            Hotel::factory()->forCity($madinah)->create([
                'supplier_id' => $suppliers->random()->id,
            ]);
        }
    }
}
