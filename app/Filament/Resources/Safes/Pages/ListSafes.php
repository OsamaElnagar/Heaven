<?php

namespace App\Filament\Resources\Safes\Pages;

use App\Filament\Resources\Safes\SafeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSafes extends ListRecords
{
    protected static string $resource = SafeResource::class;

    protected static ?string $title = 'قائمة الخزائن';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('إنشاء خزينة'),
        ];
    }
}
