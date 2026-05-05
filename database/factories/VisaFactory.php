<?php

namespace Database\Factories;

use App\Enums\VisaStatus;
use App\Models\Visa;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Visa> */
class VisaFactory extends Factory
{
    protected $model = Visa::class;

    public function definition(): array
    {
        return [
            'status' => VisaStatus::NOT_APPLIED,
            'applied_at' => null,
            'approved_at' => null,
            'expiry_date' => null,
            'visa_number' => null,
            'rejection_reason' => null,
            'notes' => null,
        ];
    }

    public function applied(): static
    {
        return $this->state(fn () => [
            'status' => VisaStatus::APPLIED,
            'applied_at' => Carbon::today()->subDays(fake()->numberBetween(1, 30)),
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status' => VisaStatus::APPROVED,
            'applied_at' => Carbon::today()->subDays(fake()->numberBetween(30, 60)),
            'approved_at' => Carbon::today()->subDays(fake()->numberBetween(1, 20)),
            'expiry_date' => Carbon::today()->addMonths(3),
            'visa_number' => fake()->numerify('VSA-##########'),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'status' => VisaStatus::REJECTED,
            'applied_at' => Carbon::today()->subDays(fake()->numberBetween(14, 45)),
            'rejection_reason' => 'بيانات غير مكتملة',
        ]);
    }
}
