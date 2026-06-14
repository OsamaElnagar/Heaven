<?php

namespace App\Filament\Resources\PayrollRuns\Actions;

use App\Models\PayrollLine;
use App\Services\PayrollService;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class BulkMarkPaidAction extends BulkAction
{
    public static function getDefaultName(): ?string
    {
        return 'bulkMarkPaid';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('تسجيل الدفع للمحدد')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->deselectRecordsAfterCompletion()
            ->schema([
                Select::make('safe_id')
                    ->label('الخزينة')
                    ->relationship('safe', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
            ])
            ->action(function (Collection $records, array $data): void {
                $payrollService = app(PayrollService::class);

                $unpaidLines = $records->filter(fn (PayrollLine $line) => ! $line->is_paid);

                if ($unpaidLines->isEmpty()) {
                    Notification::make()
                        ->title('لا توجد بنود غير مدفوعة')
                        ->warning()
                        ->send();

                    return;
                }

                DB::transaction(function () use ($unpaidLines, $data, $payrollService) {
                    $payrollService->recordBulkPayment($unpaidLines, $data['safe_id']);

                    foreach ($unpaidLines as $line) {
                        $line->paid_amount = $line->net_salary;
                        $line->remaining_amount = 0;
                        $line->is_paid = true;
                        $line->save();

                        if ($line->advances_deduction > 0) {
                            $this->repayAdvances($line);
                        }
                    }
                });

                Notification::make()
                    ->title('تم تسجيل الدفع لـ '.$unpaidLines->count().' بند')
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
