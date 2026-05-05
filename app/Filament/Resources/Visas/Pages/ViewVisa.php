<?php

namespace App\Filament\Resources\Visas\Pages;

use App\Filament\Resources\Visas\VisaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewVisa extends ViewRecord
{
    protected static string $resource = VisaResource::class;

    protected static ?string $title = 'عرض تأشيرة';

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()->label('تعديل'),
        ];
    }
}
