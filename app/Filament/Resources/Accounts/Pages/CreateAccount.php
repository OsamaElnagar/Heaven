<?php

namespace App\Filament\Resources\Accounts\Pages;

use App\Filament\Resources\Accounts\AccountResource;
use App\Services\Accounting\AccountService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateAccount extends CreateRecord
{
    protected static string $resource = AccountResource::class;

    protected static ?string $title = 'إنشاء حساب';

    protected function handleRecordCreation(array $data): Model
    {
        return app(AccountService::class)->create($data);
    }
}
