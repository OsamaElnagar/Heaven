<?php

namespace App\Filament\Resources\FiscalYears\Pages;

use App\Filament\Resources\FiscalYears\FiscalYearResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFiscalYears extends ListRecords
{
    protected static string $resource = FiscalYearResource::class;

    protected static ?string $title = 'السنوات المالية';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('إنشاء سنة مالية'),
        ];
    }
}
