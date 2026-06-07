<?php

namespace App\Filament\Resources\Visas\Pages;

use App\Filament\Resources\Visas\Actions\ExportVisaListAction;
use App\Filament\Resources\Visas\VisaResource;
use App\Filament\Resources\Visas\Widgets\VisaStatusWidget;
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

    protected function getHeaderWidgets(): array
    {
        return [
            VisaStatusWidget::class,
        ];
    }
}
