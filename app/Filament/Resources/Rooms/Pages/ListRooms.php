<?php

namespace App\Filament\Resources\Rooms\Pages;

use App\Filament\Resources\Rooms\RoomResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

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
}
