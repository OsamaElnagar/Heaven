<?php

namespace Database\Seeders;

use App\Models\Agent;
use Illuminate\Database\Seeder;

class AgentSeeder extends Seeder
{
    public function run(): void
    {
        if (Agent::count() === 0) {
            Agent::factory(4)->create();
        }
    }
}
