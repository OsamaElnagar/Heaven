<?php

namespace App\Filament\Resources\PayrollRuns\Pages;

use App\Filament\Resources\PayrollRuns\Actions\ApprovePayrollAction;
use App\Filament\Resources\PayrollRuns\Actions\DuplicatePayrollRunAction;
use App\Filament\Resources\PayrollRuns\Actions\GenerateLinesAction;
use App\Filament\Resources\PayrollRuns\Actions\PostPayrollAction;
use App\Filament\Resources\PayrollRuns\PayrollRunResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPayrollRun extends ViewRecord
{
    protected static string $resource = PayrollRunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            GenerateLinesAction::make(),
            ApprovePayrollAction::make(),
            PostPayrollAction::make(),
            DuplicatePayrollRunAction::make(),
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
