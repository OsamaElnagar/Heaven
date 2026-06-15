<?php

namespace App\Filament\Resources\Trips\Widgets;

use App\Models\Trip;
use Filament\Widgets\Widget;

class TripOccupancyWidget extends Widget
{
    public ?Trip $record = null;

    protected string $view = 'filament.resources.trips.widgets.trip-occupancy';

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $rooms = $this->record->rooms()->with('hotel.city')->get();

        $occupancy = $rooms->groupBy(fn ($room) => $room->hotel_id)
            ->map(fn ($hotelRooms) => [
                'hotel' => $hotelRooms->first()->hotel?->name ?? 'غير محدد',
                'city' => $hotelRooms->first()->hotel?->city?->name ?? 'غير محدد',
                'capacity' => $hotelRooms->sum('capacity'),
                'occupied' => $hotelRooms->sum('occupied'),
                'fill_rate' => $hotelRooms->sum('capacity') > 0
                    ? round(($hotelRooms->sum('occupied') / $hotelRooms->sum('capacity')) * 100, 1)
                    : 0,
            ])
            ->values()
            ->toArray();

        return compact('occupancy');
    }
}
