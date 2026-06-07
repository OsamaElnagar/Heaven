<?php

use App\Providers\AccountingServiceProvider;
use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\FortifyServiceProvider;

return [
    AppServiceProvider::class,
    AccountingServiceProvider::class,
    AdminPanelProvider::class,
    FortifyServiceProvider::class,
];
