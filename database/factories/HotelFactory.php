<?php

namespace Database\Factories;

use App\Models\Hotel;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Hotel> */
class HotelFactory extends Factory
{
    protected $model = Hotel::class;

    private static array $makkahHotels = [
        'فندق برج الساعة', 'فندق الهيلتون', 'فندق دار التوحيد', 'فندق سويس أوتيل',
        'فندق المروة ريحان', 'فندق حياة ريجنسي', 'فندق بولمان زمزم', 'فندق موفنبيك',
        'فندق شيراتون مكة', 'فندق رمادا',
    ];

    private static array $madinahHotels = [
        'فندق أوبروي المدينة', 'فندق دار التقوى', 'فندق أنوار المدينة', 'فندق دلة طيبة',
        'فندق شذا المدينة', 'فندق ماريوت المدينة', 'فندق كراون بلازا',
    ];

    private static array $distances = [
        '50 متر', '100 متر', '200 متر', '300 متر', '500 متر', '750 متر', '1 كم',
    ];

    public function definition(): array
    {
        $city = fake()->randomElement(['makkah', 'madinah']);
        $isMakkah = $city === 'makkah';

        return [
            'supplier_id' => Supplier::factory(),
            'name' => $isMakkah ? fake()->randomElement(self::$makkahHotels) : fake()->randomElement(self::$madinahHotels),
            'city' => $city,
            'stars' => fake()->numberBetween(3, 5),
            'distance_to_haram' => fake()->randomElement(self::$distances),
            'notes' => fake()->boolean(20) ? fake()->sentence(4) : null,
        ];
    }

    public function makkah(): static
    {
        return $this->state(fn () => ['city' => 'makkah', 'name' => fake()->randomElement(self::$makkahHotels)]);
    }

    public function madinah(): static
    {
        return $this->state(fn () => ['city' => 'madinah', 'name' => fake()->randomElement(self::$madinahHotels)]);
    }
}
