<?php

namespace App\Filament\Resources\Trips\RelationManagers;

use App\Filament\Resources\Expenses\Schemas\ExpenseForm;
use App\Filament\Resources\Expenses\Tables\ExpensesTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ExpensesRelationManager extends RelationManager
{
    protected static string $relationship = 'expenses';

    protected static ?string $title = 'المصروفات';

    public function form(Schema $schema): Schema
    {
        return ExpenseForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return ExpensesTable::configure($table);
    }
}
