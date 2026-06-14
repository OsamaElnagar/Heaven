<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        if (Client::count() === 0) {
            Client::factory(12)->create();
        }
    }
}
