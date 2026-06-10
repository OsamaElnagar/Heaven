<?php

namespace App\Filament\Resources\Rooms\Pages;

use App\Enums\RoomType;
use App\Filament\Resources\Rooms\RoomResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListRooms extends ListRecords
{
    protected static string $resource = RoomResource::class;

    protected static ?string $title = 'قائمة الغرف';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('غرفة جديدة'),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('الكل')->icon('heroicon-m-rectangle-stack'),
        ];

        foreach (RoomType::cases() as $status) {
            $tabs[$status->value] = Tab::make($status->getLabel())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', $status->value));
        }

        return $tabs;
    }
}
