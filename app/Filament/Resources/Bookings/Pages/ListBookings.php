<?php

namespace App\Filament\Resources\Bookings\Pages;

use App\Filament\Resources\Bookings\BookingResource;
use App\Filament\Resources\Bookings\Widgets\BookingsStatsWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBookings extends ListRecords
{
    protected static string $resource = BookingResource::class;

    protected static ?string $title = 'قائمة الحجوزات';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('حجز جديد'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            BookingsStatsWidget::class,
        ];
    }
}
