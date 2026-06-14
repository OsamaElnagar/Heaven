<?php

namespace App\Filament\Resources\Attendances\Pages;

use App\Filament\Resources\Attendances\Actions\BulkAttendanceAction;
use App\Filament\Resources\Attendances\Actions\BulkMarkWeekendAction;
use App\Filament\Resources\Attendances\AttendanceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected static ?string $title = 'الحضور والانصراف';

    protected function getHeaderActions(): array
    {
        return [
            BulkAttendanceAction::make(),
            BulkMarkWeekendAction::make(),
            CreateAction::make(),
        ];
    }
}
