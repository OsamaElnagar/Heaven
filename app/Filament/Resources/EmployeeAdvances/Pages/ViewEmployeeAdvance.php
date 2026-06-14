<?php

namespace App\Filament\Resources\EmployeeAdvances\Pages;

use App\Filament\Resources\EmployeeAdvances\Actions\PostAdvanceAction;
use App\Filament\Resources\EmployeeAdvances\Actions\RecordRepaymentAction;
use App\Filament\Resources\EmployeeAdvances\EmployeeAdvanceResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewEmployeeAdvance extends ViewRecord
{
    protected static string $resource = EmployeeAdvanceResource::class;

    protected static ?string $title = 'عرض سلفة موظف';

    protected function getHeaderActions(): array
    {
        return [
            PostAdvanceAction::make(),
            RecordRepaymentAction::make(),
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
