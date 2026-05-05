<?php

namespace App\Filament\Widgets;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BookingsOverviewWidget extends BaseWidget
{
    protected ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        return [
            Stat::make('حجوزات اليوم', Booking::whereDate('created_at', today())->count())
                ->description('جديد اليوم')
                ->descriptionIcon('heroicon-o-calendar')
                ->color('info')
                ->chart([7, 3, 5, 8, 4, 6, 2]),

            Stat::make('حجوزات الشهر', Booking::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count())
                ->description('إجمالي هذا الشهر')
                ->descriptionIcon('heroicon-o-document-text')
                ->color('success')
                ->chart([12, 19, 15, 22, 25, 30, 28]),

            Stat::make('حجوزات معلقة', Booking::where('status', BookingStatus::PENDING)->count())
                ->description('تحتاج تأكيد')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning')
                ->chart([4, 8, 6, 10, 7, 5, 9]),

            Stat::make('حجوزات مؤكدة', Booking::where('status', BookingStatus::CONFIRMED)->count())
                ->description('تم التأكيد')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success')
                ->chart([10, 15, 12, 18, 20, 16, 22]),
        ];
    }
}
