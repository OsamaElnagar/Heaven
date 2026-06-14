<?php

namespace App\Filament\Resources\PayrollRuns\Actions;

use App\Enums\PayrollRunStatus;
use App\Models\PayrollRun;
use App\Services\PayrollService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class PostPayrollAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'postPayroll';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('ترحيل')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->visible(fn (PayrollRun $record): bool => $record->status === PayrollRunStatus::APPROVED)
            ->requiresConfirmation()
            ->action(function (PayrollRun $record): void {
                DB::transaction(function () use ($record) {
                    $payrollService = app(PayrollService::class);

                    $payrollService->updateTotals($record);

                    try {
                        $payrollService->post($record, auth('web')->id() ?? 1);
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('خطأ في الترحيل')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();

                        return;
                    }
                });

                Notification::make()
                    ->title('تم ترحيل المسير بنجاح')
                    ->success()
                    ->send();
            });
    }
}
