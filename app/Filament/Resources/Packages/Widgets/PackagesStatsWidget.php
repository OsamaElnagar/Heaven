<?php

namespace App\Filament\Resources\Packages\Widgets;

use App\Models\Package;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PackagesStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $query = Package::query();

        return [
            Stat::make('إجمالي المقاعد', $query->sum('total_seats'))
                ->description('السعة الكلية')
                ->icon('heroicon-o-squares-2x2')
                ->color('info'),

            Stat::make('المقاعد المحجوزة', $query->sum('reserved_seats'))
                ->description($query->sum('total_seats') > 0
                    ? round($query->sum('reserved_seats') / $query->sum('total_seats') * 100).'% من الإجمالي'
                    : '0%')
                ->icon('heroicon-o-user-group')
                ->color('success'),

            Stat::make('المقاعد المتاحة', $query->sum('total_seats') - $query->sum('reserved_seats'))
                ->description('متاحة للحجز')
                ->icon('heroicon-o-check-circle')
                ->color('warning'),
        ];
    }
}
