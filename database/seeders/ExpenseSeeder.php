<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    public function run(): void
    {
        $trips = Trip::all();

        foreach ($trips as $trip) {
            $count = fake()->numberBetween(1, 4);

            for ($i = 0; $i < $count; $i++) {
                Expense::factory()->create([
                    'trip_id' => $trip->id,
                    'paid_by' => User::inRandomOrder()->first()->id,
                ]);
            }
        }
    }
}
