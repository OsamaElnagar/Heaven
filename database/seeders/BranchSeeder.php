<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        if (Branch::count() === 0) {
            Branch::factory(3)->create();
        }
    }
}
