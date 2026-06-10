<?php

namespace Database\Factories;

use App\Enums\TripStatus;
use App\Models\Package;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Trip> */
class TripFactory extends Factory
{
    protected $model = Trip::class;

    private static array $airlines = [
        'مصر للطيران' => ['MS', 'القاهرة'],
        'السعودية' => ['SV', 'جدة'],
        'العربية للطيران' => ['G9', 'الشارقة'],
        'نسما للطيران' => ['NE', 'القاهرة'],
        'فلاي ناس' => ['XY', 'الرياض'],
    ];

    public function definition(): array
    {
        $airline = fake()->randomElement(array_keys(self::$airlines));
        $code = self::$airlines[$airline][0];
        $airport = self::$airlines[$airline][1];

        $departure = Carbon::now()->addDays(fake()->numberBetween(7, 180));

        return [
            'package_id' => Package::factory(),
            'name' => 'رحلة '.fake()->numberBetween(1, 50),
            'status' => fake()->randomElement(TripStatus::cases()),
            'airline' => $airline,
            'flight_number' => $code.fake()->numberBetween(100, 999),
            'departure_at' => $departure,
            'return_at' => (clone $departure)->addDays(fake()->numberBetween(7, 30)),
            'departure_airport' => $airport,
            'notes' => fake()->boolean(30) ? fake()->sentence(4) : null,
        ];
    }
}
