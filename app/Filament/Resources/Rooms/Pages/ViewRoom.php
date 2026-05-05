<?php

namespace App\Filament\Resources\Rooms\Pages;

use App\Filament\Resources\Rooms\Actions\AssignClientAction;
use App\Filament\Resources\Rooms\RoomResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewRoom extends ViewRecord
{
    protected static string $resource = RoomResource::class;

    protected static ?string $title = 'عرض غرفة';

    protected function getHeaderActions(): array
    {
        return [
            AssignClientAction::make(),
            EditAction::make()->label('تعديل'),
        ];
    }
}
