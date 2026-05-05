<?php

namespace App\Filament\Resources\Visas\Pages;

use App\Filament\Resources\Visas\Actions\ExportVisaListAction;
use App\Filament\Resources\Visas\VisaResource;
use Filament\Resources\Pages\ListRecords;

class ListVisas extends ListRecords
{
    protected static string $resource = VisaResource::class;

    protected static ?string $title = 'قائمة التأشيرات';

    protected function getHeaderActions(): array
    {
        return [
            ExportVisaListAction::make(),
        ];
    }
}
