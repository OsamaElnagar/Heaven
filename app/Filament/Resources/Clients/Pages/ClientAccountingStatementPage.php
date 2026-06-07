<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use App\Models\Client;
use App\Support\Statement\PartyStatementPage;

class ClientAccountingStatementPage extends PartyStatementPage
{
    protected static string $resource = ClientResource::class;

    protected static ?string $title = 'كشف الحساب';

    protected static ?string $navigationLabel = 'كشف حساب ';

    protected function statementAccountId(): ?int
    {
        $client = Client::find((int) $this->record->getKey());
        if (! $client) {
            return null;
        }

        return $client->account_id;
    }

    protected function statementEntityLabel(): string
    {
        $client = Client::find((int) $this->record->getKey());

        return $client?->name ?? '-';
    }
}
