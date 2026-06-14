<?php

namespace App\Filament\Resources\PayrollRuns\Actions;

use App\Enums\PayrollRunStatus;
use App\Models\PayrollRun;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ApprovePayrollAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'approvePayroll';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('اعتماد')
            ->tooltip('اعتماد المسير بعد التأكد من صحة البنود والمبالغ')
            ->icon('heroicon-o-check-badge')
            ->color('info')
            ->visible(fn (PayrollRun $record): bool => $record->status === PayrollRunStatus::DRAFT)
            ->requiresConfirmation()
            ->action(function (PayrollRun $record): void {
                if (! $record->lines()->exists()) {
                    Notification::make()
                        ->title('لا يمكن الاعتماد')
                        ->body('يجب توليد البنود أولاً')
                        ->warning()
                        ->send();

                    return;
                }

                $record->update(['status' => PayrollRunStatus::APPROVED]);

                Notification::make()
                    ->title('تم اعتماد المسير بنجاح')
                    ->success()
                    ->send();
            });
    }
}
