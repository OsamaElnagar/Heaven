<?php

namespace App\Filament\Resources\Safes\Pages;

use App\Filament\Resources\Safes\SafeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditSafe extends EditRecord
{
    protected static string $resource = SafeResource::class;

    protected static ?string $title = 'تعديل خزينة';

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
