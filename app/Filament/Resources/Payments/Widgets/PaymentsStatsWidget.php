<?php

namespace App\Filament\Resources\Payments\Widgets;

use App\Enums\PaymentType;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PaymentsStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $query = Payment::query();

        return [
            Stat::make('العربون', number_format((clone $query)->where('type', PaymentType::DEPOSIT)->sum('amount'), 0).' ج.م')
                ->icon('heroicon-o-currency-dollar')
                ->color('info'),

            Stat::make('الأقساط', number_format((clone $query)->where('type', PaymentType::INSTALLMENT)->sum('amount'), 0).' ج.م')
                ->icon('heroicon-o-credit-card')
                ->color('warning'),

            Stat::make('الدفعات النهائية', number_format((clone $query)->where('type', PaymentType::FINAL)->sum('amount'), 0).' ج.م')
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('المسترجع', number_format((clone $query)->where('type', PaymentType::REFUND)->sum('amount'), 0).' ج.م')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('danger'),
        ];
    }
}
