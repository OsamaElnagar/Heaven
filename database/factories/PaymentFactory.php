<?php

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Enums\PaymentType;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Payment> */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        $type = fake()->randomElement(PaymentType::cases());

        return [
            'type' => $type,
            'method' => fake()->randomElement(PaymentMethod::cases()),
            'amount' => $type === PaymentType::REFUND
                ? fake()->numberBetween(500, 10000)
                : fake()->numberBetween(5000, 80000),
            'paid_at' => Carbon::now()->subDays(fake()->numberBetween(1, 90)),
            'reference' => fake()->boolean(50) ? fake()->numerify('REF-########') : null,
            'bank_name' => fake()->boolean(30) ? fake()->randomElement(['البنك الأهلي', 'بنك مصر', 'بنك القاهرة', 'البنك العربي']) : null,
            'notes' => fake()->boolean(20) ? fake()->sentence(3) : null,
        ];
    }

    public function deposit(): static
    {
        return $this->state(fn () => ['type' => PaymentType::DEPOSIT]);
    }

    public function final(): static
    {
        return $this->state(fn () => ['type' => PaymentType::FINAL]);
    }

    public function refund(): static
    {
        return $this->state(fn () => ['type' => PaymentType::REFUND]);
    }
}
