<?php

namespace Database\Factories;

use App\Enums\RoomType;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\Trip;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Room> */
class RoomFactory extends Factory
{
    protected $model = Room::class;

    public function definition(): array
    {
        $type = fake()->randomElement(RoomType::cases());
        $capacity = match ($type) {
            RoomType::SINGLE => 1,
            RoomType::DOUBLE => 2,
            RoomType::TRIPLE => 3,
            RoomType::QUAD => 4,
            RoomType::QUINT => 5,
            RoomType::SEXTUPLE => 6,
        };

        return [
            'hotel_id' => Hotel::factory(),
            'trip_id' => Trip::factory(),
            'room_number' => fake()->numberBetween(100, 9999),
            'type' => $type,
            'capacity' => $capacity,
            'occupied' => 0,
            'price_per_person' => match ($type) {
                RoomType::SINGLE => fake()->numberBetween(15000, 30000),
                RoomType::DOUBLE => fake()->numberBetween(10000, 20000),
                RoomType::TRIPLE => fake()->numberBetween(8000, 15000),
                RoomType::QUAD => fake()->numberBetween(6000, 12000),
                default => fake()->numberBetween(5000, 10000),
            },
            'notes' => fake()->boolean(15) ? fake()->sentence(3) : null,
        ];
    }
}
