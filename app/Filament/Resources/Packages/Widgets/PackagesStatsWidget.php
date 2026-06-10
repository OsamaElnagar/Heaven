<?php

namespace App\Filament\Resources\Packages\Widgets;

use App\Models\Package;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PackagesStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totals = Package::query()
            ->selectRaw('SUM(total_seats) as total, SUM(reserved_seats) as reserved')
            ->first();

        $totalSeats = (int) $totals->total;
        $reservedSeats = (int) $totals->reserved;

        return [
            Stat::make('إجمالي المقاعد', $totalSeats)
                ->description('السعة الكلية')
                ->icon('heroicon-o-squares-2x2')
                ->color('info'),

            Stat::make('المقاعد المحجوزة', $reservedSeats)
                ->description($totalSeats > 0
                    ? round($reservedSeats / $totalSeats * 100).'% من الإجمالي'
                    : '0%')
                ->icon('heroicon-o-user-group')
                ->color('success'),

            Stat::make('المقاعد المتاحة', $totalSeats - $reservedSeats)
                ->description('متاحة للحجز')
                ->icon('heroicon-o-check-circle')
                ->color('warning'),
        ];
    }
}
