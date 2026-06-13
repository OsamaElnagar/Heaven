<?php

namespace Database\Factories;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class BranchFactory extends Factory
{
    protected $model = Branch::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->companyEmail(),
            'city_id' => fake()->randomElement([1, 2]),
            'address' => fake()->address(),
            'manager_name' => fake()->name(),
            'manager_phone' => fake()->phoneNumber(),
            'commission_percentage' => fake()->randomFloat(2, 1, 10),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
