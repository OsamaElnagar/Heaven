<?php

namespace App\Filament\Resources\EmployeeAdvances\Actions;

use App\Enums\EmployeeAdvanceStatus;
use App\Models\EmployeeAdvance;
use App\Models\Safe;
use App\Services\JournalService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class RecordRepaymentAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'recordRepayment';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('تسجيل سداد')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('info')
            ->slideOver()
            ->schema([
                TextInput::make('amount')
                    ->label('المبلغ')
                    ->numeric()
                    ->required()
                    ->minValue(1),
                Select::make('safe_id')
                    ->label('الخزينة')
                    ->relationship('safe', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
            ])
            ->action(function (array $data, EmployeeAdvance $record): void {
                DB::transaction(function () use ($data, $record) {
                    $safe = Safe::findOrFail($data['safe_id']);

                    $newRepaid = $record->repaid_amount + $data['amount'];
                    $isFullyRepaid = $newRepaid >= $record->amount;

                    $record->update([
                        'repaid_amount' => $newRepaid,
                        'status' => $isFullyRepaid ? EmployeeAdvanceStatus::FULLY_REPAID : $record->status,
                    ]);

                    $lines = [
                        [
                            'account_id' => $safe->account_id,
                            'debit_amount' => $data['amount'],
                            'description' => 'سداد سلفة موظف - خزينة '.$safe->name,
                            'safe_id' => $data['safe_id'],
                            'employee_id' => $record->employee_id,
                        ],
                        [
                            'account_id' => $record->employee->advance_account_id,
                            'credit_amount' => $data['amount'],
                            'description' => 'سداد سلفة',
                            'employee_id' => $record->employee_id,
                        ],
                    ];

                    app(JournalService::class)->post('employee_advance', $record->id, $lines);
                });

                Notification::make()
                    ->title('تم تسجيل السداد بنجاح')
                    ->success()
                    ->send();
            });
    }
}
