<?php

namespace App\Filament\Resources\Bookings\Pages;

use App\Enums\BookingStatus;
use App\Filament\Resources\Bookings\BookingResource;
use App\Filament\Resources\Bookings\Widgets\BookingsStatsWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

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

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('الكل')->icon('heroicon-m-rectangle-stack'),
        ];

        foreach (BookingStatus::cases() as $status) {
            $tabs[$status->value] = Tab::make($status->getLabel())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', $status->value))
                ->icon($status->getIcon());
        }

        return $tabs;
    }
}
