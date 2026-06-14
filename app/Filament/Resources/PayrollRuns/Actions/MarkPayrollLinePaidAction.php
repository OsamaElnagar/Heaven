<?php

namespace App\Filament\Resources\PayrollRuns\Actions;

use App\Models\PayrollLine;
use App\Services\PayrollService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class MarkPayrollLinePaidAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'markPaid';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('دفع')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->slideOver()
            ->visible(fn (PayrollLine $record): bool => ! $record->is_paid)
            ->schema([
                TextInput::make('paid_amount')
                    ->label('المبلغ المدفوع')
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
            ->mountUsing(function (PayrollLine $record): array {
                return [
                    'paid_amount' => $record->remaining_amount > 0 ? $record->remaining_amount : $record->net_salary,
                    'safe_id' => $record->safe_id,
                ];
            })
            ->action(function (array $data, PayrollLine $record): void {
                $payrollService = app(PayrollService::class);

                DB::transaction(function () use ($data, $record, $payrollService) {
                    $wasPaid = $record->is_paid;
                    $record->paid_amount = ($record->paid_amount ?? 0) + $data['paid_amount'];
                    $record->remaining_amount = $record->net_salary - $record->paid_amount;
                    $record->is_paid = $record->remaining_amount <= 0;
                    $record->safe_id = $data['safe_id'] ?? $record->safe_id;
                    $record->save();

                    $payrollService->recordPayment($record, $data['paid_amount'], $data['safe_id']);

                    if (! $wasPaid && $record->is_paid && $record->advances_deduction > 0) {
                        $this->repayAdvances($record);
                    }
                });

                Notification::make()
                    ->title('تم تسجيل الدفع بنجاح')
                    ->success()
                    ->send();
            });
    }

    private function repayAdvances(PayrollLine $line): void
    {
        $remainingDeduction = $line->advances_deduction;

        $activeAdvances = $line->employee->employeeAdvances()
            ->where('status', 'active')
            ->get();

        foreach ($activeAdvances as $advance) {
            if ($remainingDeduction <= 0) {
                break;
            }

            $outstanding = $advance->amount - $advance->repaid_amount;

            if ($outstanding <= 0) {
                continue;
            }

            $toRepay = min($outstanding, (int) floor($advance->amount / max($advance->installments, 1)));
            $toRepay = min($toRepay, $remainingDeduction);

            $advance->repaid_amount += $toRepay;
            $advance->save();

            $remainingDeduction -= $toRepay;
        }
    }
}
