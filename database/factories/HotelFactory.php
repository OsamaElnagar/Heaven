<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Hotel;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Hotel> */
class HotelFactory extends Factory
{
    protected $model = Hotel::class;

    private static array $distances = [
        '50 متر', '100 متر', '200 متر', '300 متر', '500 متر', '750 متر', '1 كم',
    ];

    public function definition(): array
    {
        $city = City::inRandomOrder()->first();

        return [
            'supplier_id' => Supplier::factory(),
            'name' => 'فندق '.$city->name_ar.' '.fake()->unique()->randomNumber(3),
            'city_id' => $city->id,
            'stars' => fake()->numberBetween(3, 5),
            'distance_to_haram' => fake()->randomElement(self::$distances),
            'notes' => fake()->boolean(20) ? fake()->sentence(4) : null,
        ];
    }

    public function forCity(City $city): static
    {
        return $this->state(fn () => ['city_id' => $city->id]);
    }
}
