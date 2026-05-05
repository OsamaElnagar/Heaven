<?php

namespace App\Filament\Widgets;

use App\Enums\VisaStatus;
use App\Models\Visa;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VisaStatusWidget extends BaseWidget
{
    protected ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $total = Visa::count();

        return [
            Stat::make('لم تتقدم', Visa::where('status', VisaStatus::NOT_APPLIED)->count())
                ->description($total > 0 ? round(Visa::where('status', VisaStatus::NOT_APPLIED)->count() / $total * 100).'%' : '0%')
                ->color('gray')
                ->icon('heroicon-o-minus-circle'),

            Stat::make('تم التقديم', Visa::where('status', VisaStatus::APPLIED)->count())
                ->description($total > 0 ? round(Visa::where('status', VisaStatus::APPLIED)->count() / $total * 100).'%' : '0%')
                ->color('warning')
                ->icon('heroicon-o-paper-airplane'),

            Stat::make('موافق عليها', Visa::where('status', VisaStatus::APPROVED)->count())
                ->description($total > 0 ? round(Visa::where('status', VisaStatus::APPROVED)->count() / $total * 100).'%' : '0%')
                ->color('success')
                ->icon('heroicon-o-check-badge'),

            Stat::make('مرفوضة', Visa::where('status', VisaStatus::REJECTED)->count())
                ->description($total > 0 ? round(Visa::where('status', VisaStatus::REJECTED)->count() / $total * 100).'%' : '0%')
                ->color('danger')
                ->icon('heroicon-o-x-mark'),
        ];
    }
}
