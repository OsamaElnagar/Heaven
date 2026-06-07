<?php

namespace App\Filament\Resources\FiscalYears\Actions;

use App\Enums\FiscalYearStatus;
use App\Models\FiscalYear;
use App\Services\Accounting\FiscalYearService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ReopenYearAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'reopenYear';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('إعادة فتح السنة')
            ->icon('heroicon-o-lock-open')
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading('إعادة فتح السنة المالية')
            ->modalDescription('هل أنت متأكد من إعادة فتح السنة المالية؟')
            ->modalSubmitActionLabel('نعم، إعادة فتح')
            ->visible(fn (FiscalYear $record): bool => $record->status === FiscalYearStatus::CLOSED)
            ->action(function (FiscalYear $record): void {
                try {
                    app(FiscalYearService::class)->reopen($record);

                    Notification::make()
                        ->title('تم إعادة فتح السنة المالية بنجاح')
                        ->success()
                        ->send();
                } catch (\InvalidArgumentException $e) {
                    Notification::make()
                        ->title($e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }
}
