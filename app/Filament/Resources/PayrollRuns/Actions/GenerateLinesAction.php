<?php

namespace App\Filament\Resources\PayrollRuns\Actions;

use App\Enums\PayrollRunStatus;
use App\Models\PayrollRun;
use App\Services\PayrollService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class GenerateLinesAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'generateLines';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('توليد البنود')
            ->tooltip('توليد بنود الرواتب للموظفين النشطين في هذا الشهر')
            ->icon('heroicon-o-list-bullet')
            ->color('gray')
            ->visible(fn (PayrollRun $record): bool => $record->status === PayrollRunStatus::DRAFT)
            ->requiresConfirmation()
            ->action(function (PayrollRun $record): void {
                $payrollService = app(PayrollService::class);

                try {
                    $generatedCount = $payrollService->generateLines($record);
                } catch (\InvalidArgumentException $e) {
                    Notification::make()
                        ->title('لا يمكن توليد البنود')
                        ->body($e->getMessage())
                        ->warning()
                        ->send();

                    return;
                }

                if ($generatedCount === 0) {
                    Notification::make()
                        ->title('لا يوجد موظفون نشطون')
                        ->warning()
                        ->send();

                    return;
                }

                $payrollService->updateTotals($record);

                Notification::make()
                    ->title('تم توليد '.$generatedCount.' بند للموظفين النشطين')
                    ->success()
                    ->send();
            });
    }
}
