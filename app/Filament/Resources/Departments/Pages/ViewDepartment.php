<?php

namespace App\Filament\Resources\Departments\Pages;

use App\Filament\Resources\Departments\DepartmentResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDepartment extends ViewRecord
{
    protected static string $resource = DepartmentResource::class;

    protected static ?string $title = 'عرض قسم';

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
