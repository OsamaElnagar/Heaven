<?php

namespace App\Filament\Resources\PackageTypes\Pages;

use App\Filament\Resources\PackageTypes\PackageTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPackageType extends EditRecord
{
    protected static string $resource = PackageTypeResource::class;

    protected static ?string $title = 'تعديل نوع الباقة';

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
