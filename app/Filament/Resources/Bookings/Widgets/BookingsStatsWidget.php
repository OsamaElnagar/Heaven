<?php

namespace App\Filament\Resources\Bookings\Widgets;

use App\Models\Booking;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class BookingsStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $query = Booking::query();

        return [
            Stat::make('صافي المبيعات', number_format($query->sum('net_price'), 0).' ج.م')
                ->description('إجمالي قيمة الحجوزات')
                ->icon('heroicon-o-currency-dollar')
                ->color('info'),

            Stat::make('المبلغ المحصل', number_format($query->sum('paid_amount'), 0).' ج.م')
                ->description('إجمالي المدفوع')
                ->icon('heroicon-o-banknotes')
                ->color('success'),

            Stat::make('المبلغ المتبقي', number_format($query->sum(DB::raw('net_price - paid_amount')), 0).' ج.م')
                ->description('المستحق')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger'),
        ];
    }
}
