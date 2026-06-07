<?php

namespace App\Filament\Pages\Reports\Widgets;

use App\Models\Expense;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class ExpenseSummaryWidget extends BaseWidget
{
    protected ?string $pollingInterval = null;

    public ?string $dateFrom = null;

    public ?string $dateTo = null;

    public ?string $status = null;

    public function getSummary(): array
    {
        return static::computeSummary($this->dateFrom, $this->dateTo, $this->status);
    }

    public static function computeSummary(?string $dateFrom, ?string $dateTo, ?string $status): array
    {
        $query = Expense::query()
            ->when($dateFrom, fn ($q) => $q->whereDate('paid_at', '>=', Carbon::parse($dateFrom)))
            ->when($dateTo, fn ($q) => $q->whereDate('paid_at', '<=', Carbon::parse($dateTo)))
            ->when($status, fn ($q) => $q);

        $total = (int) $query->sum('amount');
        $count = (clone $query)->count();

        return [
            'total' => $total,
            'count' => $count,
        ];
    }

    protected function getStats(): array
    {
        $summary = $this->getSummary();

        return [
            Stat::make('إجمالي المصروفات', number_format($summary['total']).' ج.م')
                ->description('مجموع مبالغ المصروفات في الفترة')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),
            Stat::make('عدد المصروفات', number_format($summary['count']))
                ->description('عدد العمليات في الفترة')
                ->descriptionIcon('heroicon-m-list-bullet')
                ->color('info'),
        ];
    }
}
