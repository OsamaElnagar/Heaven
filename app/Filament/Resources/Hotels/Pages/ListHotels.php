<?php

namespace App\Filament\Resources\Hotels\Pages;

use App\Filament\Resources\Hotels\HotelResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHotels extends ListRecords
{
    protected static string $resource = HotelResource::class;

    protected static ?string $title = 'قائمة الفنادق';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('فندق جديد'),
        ];
    }
}
