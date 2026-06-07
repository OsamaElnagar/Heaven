<?php

namespace App\Filament\Resources\Bookings\RelationManagers;

use App\Filament\Resources\ReceiptVouchers\Schemas\ReceiptVoucherForm;
use App\Filament\Resources\ReceiptVouchers\Tables\ReceiptVouchersTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'receiptVouchers';

    protected static ?string $title = 'سندات القبض';

    public function form(Schema $schema): Schema
    {
        return ReceiptVoucherForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return ReceiptVouchersTable::configure($table);
    }
}
