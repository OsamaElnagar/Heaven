<?php

namespace App\Filament\Resources\Accounts\Pages;

use App\Filament\Resources\Accounts\AccountResource;
use App\Filament\Resources\Accounts\Actions\ActivateAccountAction;
use App\Filament\Resources\Accounts\Actions\DeactivateAccountAction;
use App\Services\Accounting\AccountService;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditAccount extends EditRecord
{
    protected static string $resource = AccountResource::class;

    protected static ?string $title = 'تعديل حساب';

    protected function getHeaderActions(): array
    {
        return [
            ActivateAccountAction::make(),
            DeactivateAccountAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return app(AccountService::class)->update($record, $data);
    }
}
