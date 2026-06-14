<?php

namespace App\Providers;

use App\Services\Accounting\AccountService;
use App\Services\Accounting\DocumentSequenceService;
use App\Services\Accounting\FiscalYearService;
use App\Services\Accounting\JournalEntryService;
use App\Services\JournalService;
use App\Services\PayrollService;
use Illuminate\Support\ServiceProvider;

class AccountingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(JournalEntryService::class, fn ($app) => new JournalEntryService);
        $this->app->singleton(AccountService::class, fn ($app) => new AccountService);
        $this->app->singleton(FiscalYearService::class, fn ($app) => new FiscalYearService);
        $this->app->singleton(DocumentSequenceService::class, fn ($app) => new DocumentSequenceService);
        $this->app->singleton(JournalService::class, fn ($app) => new JournalService);
        $this->app->singleton(PayrollService::class, fn ($app) => new PayrollService);
    }

    public function boot(): void
    {
        //
    }
}
