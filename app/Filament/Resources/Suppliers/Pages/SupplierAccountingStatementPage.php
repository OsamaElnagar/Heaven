<?php

namespace App\Filament\Resources\Suppliers\Pages;

use App\Filament\Resources\Suppliers\SupplierResource;
use App\Support\Statement\PartyStatementPage;

class SupplierAccountingStatementPage extends PartyStatementPage
{
    protected static string $resource = SupplierResource::class;

    protected static ?string $title = 'كشف الحساب';

    protected static ?string $navigationLabel = 'كشف حساب ';

    protected function statementAccountId(): ?int
    {
        return $this->record->account_id;
    }

    protected function statementEntityLabel(): string
    {
        return $this->record->name ?? '-';
    }
}
