<?php

namespace App\Console\Commands;

use App\Services\VisaService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:expire-overdue-visas')]
#[Description('Mark approved visas as expired when their expiry date has passed')]
class ExpireOverdueVisas extends Command
{
    public function handle(VisaService $visaService): int
    {
        $count = $visaService->expireOverdueVisas();

        $this->info("Expired {$count} overdue visa(s).");

        return Command::SUCCESS;
    }
}
