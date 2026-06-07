<?php

namespace Database\Seeders;

use App\Models\FiscalYear;
use App\Services\Accounting\DocumentSequenceService;
use Illuminate\Database\Seeder;

class FiscalYearSeeder extends Seeder
{
    public function run(): void
    {
        $fiscalYear = FiscalYear::firstOrCreate(
            ['name' => 'السنة المالية 2026'],
            [
                'starts_at' => '2026-01-01',
                'ends_at' => '2026-12-31',
                'status' => 'open',
                'closed_at' => null,
                'closed_by' => null,
            ]
        );

        if ($fiscalYear->wasRecentlyCreated) {
            app(DocumentSequenceService::class)->initializeForFiscalYear($fiscalYear);
        }
    }
}
