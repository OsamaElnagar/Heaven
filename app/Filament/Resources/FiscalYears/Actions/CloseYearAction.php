<?php

namespace App\Filament\Resources\FiscalYears\Actions;

use App\Filament\Resources\JournalEntries\JournalEntryResource;
use App\Models\FiscalYear;
use App\Services\Accounting\FiscalYearService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class CloseYearAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'closeYear';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('إقفال السنة')
            ->icon('heroicon-o-lock-closed')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('إقفال السنة المالية')
            ->modalDescription('هل أنت متأكد من إقفال السنة المالية؟ لن يمكن الترحيل على هذه السنة بعد الإقفال.')
            ->modalSubmitActionLabel('نعم، إقفال السنة')
            ->visible(fn (FiscalYear $record): bool => $record->status?->value === 'open')
            ->action(function (FiscalYear $record): void {
                try {
                    app(FiscalYearService::class)->close($record, auth('web')->id());

                    Notification::make()
                        ->title('تم إقفال السنة المالية بنجاح')
                        ->success()
                        ->send();
                } catch (\InvalidArgumentException $e) {
                    Notification::make()
                        ->title($e->getMessage())
                        ->danger()
                        ->persistent()
                        ->actions([
                            Action::make('viewDrafts')
                                ->label('عرض القيود غير المرحّلة')
                                ->url(JournalEntryResource::getUrl('index', [
                                    'filters' => [
                                        'status' => ['value' => 'draft'],
                                        'fiscal_year_id' => ['value' => (string) $record->id],
                                    ],
                                ])),
                        ])
                        ->send();
                }
            });
    }
}
