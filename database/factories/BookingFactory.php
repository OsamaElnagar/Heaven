<?php

namespace Database\Factories;

use App\Enums\BookingStatus;
use App\Enums\RoomType;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Booking> */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $totalPrice = fake()->numberBetween(50000, 200000);
        $discount = fake()->boolean(25) ? fake()->numberBetween(1000, 10000) : 0;

        return [
            'reference' => 'BK-'.now()->year.'-'.str_pad(fake()->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT),
            'status' => fake()->randomElement(BookingStatus::cases()),
            'room_type' => fake()->randomElement(RoomType::cases()),
            'total_price' => $totalPrice,
            'discount' => $discount,
            'net_price' => $totalPrice - $discount,
            'paid_amount' => 0,
            'due_date' => now()->addDays(fake()->numberBetween(7, 60)),
            'notes' => fake()->boolean(20) ? fake()->sentence(3) : null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => BookingStatus::PENDING]);
    }

    public function confirmed(): static
    {
        return $this->state(fn () => ['status' => BookingStatus::CONFIRMED]);
    }

    public function completed(): static
    {
        return $this->state(fn () => ['status' => BookingStatus::COMPLETED]);
    }
}
