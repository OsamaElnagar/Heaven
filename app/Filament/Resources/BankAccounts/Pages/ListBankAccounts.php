<?php

namespace App\Filament\Resources\BankAccounts\Pages;

use App\Filament\Resources\BankAccounts\BankAccountResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBankAccounts extends ListRecords
{
    protected static string $resource = BankAccountResource::class;

    protected static ?string $title = 'قائمة الحسابات البنكية';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('إضافة حساب بنكي'),
        ];
    }
}
