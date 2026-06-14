<?php

namespace App\Filament\Resources\EmployeeAdvances\Actions;

use App\Enums\EmployeeAdvanceStatus;
use App\Models\EmployeeAdvance;
use App\Services\JournalService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class PostAdvanceAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'postAdvance';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('ترحيل')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->visible(fn (EmployeeAdvance $record): bool => $record->status === EmployeeAdvanceStatus::ACTIVE)
            ->action(function (EmployeeAdvance $record): void {
                DB::transaction(function () use ($record) {
                    $lines = [];

                    $lines[] = [
                        'account_id' => $record->employee->advance_account_id ?? $record->employee->account_id,
                        'debit_amount' => $record->amount,
                        'description' => 'سلفة موظف - '.$record->employee->name,
                        'employee_id' => $record->employee_id,
                    ];

                    if ($record->safe_id) {
                        $lines[] = [
                            'account_id' => $record->safe->account_id,
                            'credit_amount' => $record->amount,
                            'description' => 'صرف سلفة من خزينة '.$record->safe->name,
                            'employee_id' => $record->employee_id,
                        ];
                    }

                    app(JournalService::class)->post('employee_advance', $record->id, $lines);
                });

                Notification::make()
                    ->title('تم ترحيل السلفة بنجاح')
                    ->success()
                    ->send();
            });
    }
}
