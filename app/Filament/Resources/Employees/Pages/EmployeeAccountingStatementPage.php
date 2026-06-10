<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Resources\Employees\EmployeeResource;
use App\Support\Statement\PartyStatementPage;

class EmployeeAccountingStatementPage extends PartyStatementPage
{
    protected static string $resource = EmployeeResource::class;

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
