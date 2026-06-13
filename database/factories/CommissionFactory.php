<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Commission;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommissionFactory extends Factory
{
    protected $model = Commission::class;

    public function definition(): array
    {
        $booking = Booking::factory();

        return [
            'booking_id' => $booking,
            'commission_type' => 'percentage',
            'commission_rate' => fake()->randomFloat(2, 1, 10),
            'amount' => fake()->randomFloat(2, 100, 5000),
            'status' => 'pending',
        ];
    }
}
