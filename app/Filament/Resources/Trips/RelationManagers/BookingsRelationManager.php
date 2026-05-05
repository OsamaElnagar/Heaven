<?php

namespace App\Filament\Resources\Trips\RelationManagers;

use App\Filament\Resources\Bookings\Schemas\BookingForm;
use App\Filament\Resources\Bookings\Tables\BookingsTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class BookingsRelationManager extends RelationManager
{
    protected static string $relationship = 'bookings';

    protected static ?string $title = 'الحجوزات';

    public function form(Schema $schema): Schema
    {
        return BookingForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return BookingsTable::configure($table);
    }
}
