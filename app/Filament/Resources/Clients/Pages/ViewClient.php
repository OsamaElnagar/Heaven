<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\Actions\ExportClientCardAction;
use App\Filament\Resources\Clients\Actions\ViewStatementAction;
use App\Filament\Resources\Clients\ClientResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewClient extends ViewRecord
{
    protected static string $resource = ClientResource::class;

    protected static ?string $title = 'عرض عميل';

    protected function getHeaderActions(): array
    {
        return [
            ExportClientCardAction::make(),
            ViewStatementAction::make(),
            EditAction::make()->label('تعديل'),
        ];
    }
}
