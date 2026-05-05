<?php

namespace App\Filament\Resources\Visas\Pages;

use App\Filament\Resources\Visas\VisaResource;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditVisa extends EditRecord
{
    protected static string $resource = VisaResource::class;

    protected static ?string $title = 'تعديل تأشيرة';

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()->label('عرض'),
        ];
    }
}
