<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use App\Support\Statement\PartyStatementPage;

class ClientAccountingStatementPage extends PartyStatementPage
{
    protected static string $resource = ClientResource::class;

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
