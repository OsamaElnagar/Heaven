<?php

namespace App\Filament\Resources\Clients\RelationManagers;

use App\Filament\Resources\Payments\Schemas\PaymentForm;
use App\Filament\Resources\Payments\Tables\PaymentsTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static ?string $title = 'المدفوعات';

    protected static string $relationship = 'payments';

    public function form(Schema $schema): Schema
    {
        return PaymentForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return PaymentsTable::configure($table);
    }
}
