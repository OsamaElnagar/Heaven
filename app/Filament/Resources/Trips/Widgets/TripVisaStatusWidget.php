<?php

namespace App\Filament\Resources\Trips\Widgets;

use App\Enums\VisaStatus;
use App\Models\Trip;
use App\Services\ReportService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TripVisaStatusWidget extends BaseWidget
{
    public ?Trip $record = null;

    protected function getStats(): array
    {
        $visas = (new ReportService)->visaDashboard($this->record);

        return collect(VisaStatus::cases())->map(
            fn (VisaStatus $case) => Stat::make($case->getLabel(), $visas[$case->getLabel()] ?? 0)
                ->color($case->getColor())
                ->icon($case->getIcon())
        )->toArray();
    }
}
