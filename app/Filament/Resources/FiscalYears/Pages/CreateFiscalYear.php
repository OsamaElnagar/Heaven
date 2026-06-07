<?php

namespace App\Filament\Resources\FiscalYears\Pages;

use App\Filament\Resources\FiscalYears\FiscalYearResource;
use App\Services\Accounting\FiscalYearService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateFiscalYear extends CreateRecord
{
    protected static string $resource = FiscalYearResource::class;

    protected static ?string $title = 'إنشاء سنة مالية';

    protected function handleRecordCreation(array $data): Model
    {
        return app(FiscalYearService::class)->create($data);
    }
}
