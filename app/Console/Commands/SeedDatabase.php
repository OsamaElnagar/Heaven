<?php

namespace App\Console\Commands;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

#[Signature('seed {--fresh : Run migrate:fresh before seeding} {--production : Only seed foundational data (no demo clients, bookings, financials)}')]
#[Description('Seed the database with optional foundational-only mode')]
class SeedDatabase extends Command
{
    public function handle(): int
    {
        $production = $this->option('production');

        if ($this->option('fresh')) {
            $this->info('Dropping all tables and re-running migrations...');
            Artisan::call('migrate:fresh', [], $this->getOutput());
            $this->newLine();
        }

        $this->info($production ? 'Seeding foundational data only...' : 'Seeding all data...');

        $seeder = app(DatabaseSeeder::class);
        $seeder->production = $production;
        $seeder->setCommand($this);
        $seeder->run();

        $this->info('Done!');

        return self::SUCCESS;
    }
}
