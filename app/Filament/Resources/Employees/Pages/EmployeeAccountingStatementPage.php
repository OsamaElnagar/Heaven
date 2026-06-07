<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Resources\Employees\EmployeeResource;
use App\Models\Employee;
use App\Support\Statement\PartyStatementPage;

class EmployeeAccountingStatementPage extends PartyStatementPage
{
    protected static string $resource = EmployeeResource::class;

    protected static ?string $title = 'كشف الحساب';

    protected static ?string $navigationLabel = 'كشف حساب ';

    protected function statementAccountId(): ?int
    {
        $employee = Employee::find((int) $this->record->getKey());

        return $employee?->account_id;
    }

    protected function statementEntityLabel(): string
    {
        $employee = Employee::find((int) $this->record->getKey());

        return $employee?->name ?? '-';
    }
}
