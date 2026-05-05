<?php

namespace App\Filament\Widgets;

use App\Enums\PaymentType;
use App\Models\Booking;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class RevenueWidget extends BaseWidget
{
    protected ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $totalPaid = (float) Payment::whereNot('type', PaymentType::REFUND->value)->sum('amount');
        $totalRefunded = (float) Payment::where('type', PaymentType::REFUND->value)->sum('amount');
        $totalOutstanding = (float) Booking::whereIn('status', ['pending', 'confirmed'])
            ->sum(DB::raw('net_price - paid_amount'));

        return [
            Stat::make('إجمالي المحصل', number_format($totalPaid, 0).' ج.م')
                ->description('المدفوعات المستلمة')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('success')
                ->chart([150000, 220000, 180000, 280000, 320000, 250000, 350000]),

            Stat::make('المستحق', number_format($totalOutstanding, 0).' ج.م')
                ->description('متأخرات')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color('danger')
                ->chart([80000, 65000, 70000, 55000, 48000, 52000, 40000]),

            Stat::make('المسترجع', number_format($totalRefunded, 0).' ج.م')
                ->description('مبالغ مستردة')
                ->descriptionIcon('heroicon-o-arrow-uturn-left')
                ->color('gray')
                ->chart([5000, 8000, 3000, 12000, 0, 4000, 2000]),

            Stat::make('متوسط قيمة الحجز', number_format(Booking::avg('net_price') ?? 0, 0).' ج.م')
                ->description('متوسط السعر')
                ->descriptionIcon('heroicon-o-chart-bar')
                ->color('info')
                ->chart([85000, 90000, 82000, 95000, 88000, 92000, 87000]),
        ];
    }
}
