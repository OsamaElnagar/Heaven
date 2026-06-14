<?php

namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\Room;
use App\Models\Trip;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        if (Room::count() > 0) {
            return;
        }

        $trips = Trip::with('package')->get();
        $hotels = Hotel::all();

        foreach ($trips as $trip) {
            $tripHotels = $hotels->random(fake()->numberBetween(1, min(2, $hotels->count())));

            foreach ($tripHotels as $hotel) {
                $roomCount = fake()->numberBetween(3, 8);

                for ($i = 0; $i < $roomCount; $i++) {
                    Room::factory()->create([
                        'hotel_id' => $hotel->id,
                        'trip_id' => $trip->id,
                    ]);
                }
            }
        }
    }
}
