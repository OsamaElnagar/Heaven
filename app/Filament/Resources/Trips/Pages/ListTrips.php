<?php

namespace App\Filament\Resources\Trips\Pages;

use App\Filament\Resources\Trips\TripResource;
use App\Filament\Resources\Trips\Widgets\UpcomingTripsWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTrips extends ListRecords
{
    protected static string $resource = TripResource::class;

    protected static ?string $title = 'قائمة الرحلات';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('رحلة جديدة'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            UpcomingTripsWidget::class,
        ];
    }
}
