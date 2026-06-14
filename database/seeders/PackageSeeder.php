<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        if (Package::count() === 0) {
            Package::factory(8)->create();
        }
    }
}
