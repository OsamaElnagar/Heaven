<?php

namespace App\Filament\Resources\PayrollRuns\Actions;

use App\Filament\Resources\PayrollRuns\PayrollRunResource;
use App\Models\PayrollRun;
use App\Services\PayrollService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class DuplicatePayrollRunAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'duplicatePayrollRun';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('تكرار')
            ->icon('heroicon-o-document-duplicate')
            ->color('info')
            ->requiresConfirmation()
            ->action(function (PayrollRun $record): void {
                $payrollService = app(PayrollService::class);

                try {
                    $newRun = $payrollService->duplicateRun($record, auth('web')->id() ?? 1);
                } catch (\InvalidArgumentException $e) {
                    Notification::make()
                        ->title('لا يمكن التكرار')
                        ->body($e->getMessage())
                        ->warning()
                        ->send();

                    return;
                }

                Notification::make()
                    ->title('تم تكرار المسير')
                    ->body('تم إنشاء مسير جديد للشهر التالي')
                    ->success()
                    ->action('عرض', fn () => redirect(PayrollRunResource::getUrl('view', ['record' => $newRun])))
                    ->send();
            });
    }
}
