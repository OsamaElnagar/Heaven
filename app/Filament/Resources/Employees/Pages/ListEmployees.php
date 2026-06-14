<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Resources\Attendances\Actions\BulkAttendanceAction;
use App\Filament\Resources\Employees\EmployeeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEmployees extends ListRecords
{
    protected static string $resource = EmployeeResource::class;

    protected static ?string $title = 'قائمة الموظفين';

    protected function getHeaderActions(): array
    {
        return [
            BulkAttendanceAction::make(),
            CreateAction::make()->label('موظف جديد'),
        ];
    }
}
