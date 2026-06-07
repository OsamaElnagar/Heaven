<?php

namespace App\Filament\Resources\Packages\Pages;

use App\Filament\Resources\Packages\PackageResource;
use App\Filament\Resources\Packages\Widgets\PackagesStatsWidget;
use App\Filament\Resources\Packages\Widgets\SeatOccupancyWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPackages extends ListRecords
{
    protected static string $resource = PackageResource::class;

    protected static ?string $title = 'قائمة الباقات';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('باقة جديدة'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PackagesStatsWidget::class,
            SeatOccupancyWidget::class,
        ];
    }
}
