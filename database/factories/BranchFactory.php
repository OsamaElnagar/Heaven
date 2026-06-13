<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\City;
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
            'city_id' => City::factory(),
            'address' => fake()->address(),
            'manager_name' => fake()->name(),
            'manager_phone' => fake()->phoneNumber(),
            'commission_percentage' => fake()->randomFloat(2, 1, 10),
            'is_active' => true,
        ];
    }
}
