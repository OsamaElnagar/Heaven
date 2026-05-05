<?php

namespace App\Filament\Resources\Expenses\Actions;

use App\Models\Expense;
use Filament\Actions\Action;

class ExportExpensesAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'exportExpenses';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('تصدير المصروفات')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('gray')
            ->action(function () {
                $expenses = Expense::with('trip', 'paidBy')
                    ->latest('paid_at')
                    ->get()
                    ->map(fn ($e) => [
                        'الوصف' => $e->description,
                        'الفئة' => $e->category,
                        'المبلغ' => $e->amount,
                        'طريقة الدفع' => $e->payment_method?->getLabel(),
                        'التاريخ' => $e->paid_at?->format('Y-m-d'),
                        'الرحلة' => $e->trip?->name,
                    ])
                    ->toArray();

                $filename = 'expenses-'.now()->format('Y-m-d').'.csv';
                $headers = [
                    'Content-Type' => 'text/csv; charset=UTF-8',
                    'Content-Disposition' => 'attachment; filename="'.$filename.'"',
                ];
                $callback = function () use ($expenses) {
                    $file = fopen('php://output', 'w');
                    fwrite($file, "\xEF\xBB\xBF");
                    if (! empty($expenses)) {
                        fputcsv($file, array_keys($expenses[0]));
                        foreach ($expenses as $row) {
                            fputcsv($file, $row);
                        }
                    }
                    fclose($file);
                };

                return response()->stream($callback, 200, $headers);
            });
    }
}
