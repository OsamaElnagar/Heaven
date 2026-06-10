<?php

namespace App\Filament\Resources\Trips\Pages;

use App\Enums\TripStatus;
use App\Filament\Resources\Trips\TripResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

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

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('الكل')->icon('heroicon-m-rectangle-stack'),
        ];

        foreach (TripStatus::cases() as $status) {
            $tabs[$status->value] = Tab::make($status->getLabel())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', $status->value))
                ->icon($status->getIcon());
        }

        return $tabs;
    }
}
