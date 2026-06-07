<?php

namespace App\Filament\Resources\FiscalYears\Pages;

use App\Filament\Resources\FiscalYears\Actions\CloseYearAction;
use App\Filament\Resources\FiscalYears\Actions\ReopenYearAction;
use App\Filament\Resources\FiscalYears\FiscalYearResource;
use App\Services\Accounting\FiscalYearService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditFiscalYear extends EditRecord
{
    protected static string $resource = FiscalYearResource::class;

    protected static ?string $title = 'تعديل سنة مالية';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('openingBalances')
                ->label('الأرصدة الافتتاحية')
                ->icon('heroicon-o-scale')
                ->color('info')
                ->url(fn ($record) => FiscalYearResource::getUrl('opening-balances', ['record' => $record])),
            CloseYearAction::make(),
            ReopenYearAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return app(FiscalYearService::class)->update($record, $data);
    }
}
