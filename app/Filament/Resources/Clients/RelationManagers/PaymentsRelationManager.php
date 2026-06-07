<?php

namespace App\Filament\Resources\Clients\RelationManagers;

use App\Filament\Resources\ReceiptVouchers\Tables\ReceiptVouchersTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static ?string $title = 'سندات القبض';

    protected static string $relationship = 'payments';

    public function table(Table $table): Table
    {
        return ReceiptVouchersTable::configure($table);
    }
}
