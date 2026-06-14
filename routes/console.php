<?php

use App\Console\Commands\ExpireOverdueVisas;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(ExpireOverdueVisas::class)->daily();

Schedule::command('queue:work --stop-when-empty')->everyMinute()->withoutOverlapping();

Schedule::command('backup:clean')->wednesdays()->at('03:00')
    ->onFailure(fn() => Notification::make()
        ->title('فشل تنظيف النسخة الاحتياطية')
        ->body('فشلت عملية تنظيف النسخة الاحتياطية اليومية.')
        ->danger()
        ->icon('heroicon-o-x-circle')
        ->sendToDatabase(User::all()));

Schedule::command('backup:run')->dailyAt('03:00')
    ->onFailure(fn() => Notification::make()
        ->title('فشل النسخ الاحتياطي')
        ->body('فشلت عملية النسخ الاحتياطي اليومية.')
        ->danger()
        ->icon('heroicon-o-x-circle')
        ->sendToDatabase(User::all()));