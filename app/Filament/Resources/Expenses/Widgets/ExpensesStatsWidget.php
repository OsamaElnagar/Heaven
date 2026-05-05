<?php

namespace App\Filament\Resources\Expenses\Widgets;

use App\Models\Expense;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ExpensesStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $query = Expense::query();

        return [
            Stat::make('إجمالي المصروفات', number_format($query->sum('amount'), 0).' ج.م')
                ->icon('heroicon-o-receipt-percent')
                ->color('danger'),
        ];
    }
}
