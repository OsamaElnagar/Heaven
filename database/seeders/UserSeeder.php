<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@heaven.test'],
            ['name' => 'أسامة النجار', 'password' => bcrypt('password')]
        );

        if (User::count() < 2) {
            User::factory(2)->create();
        }
    }
}
