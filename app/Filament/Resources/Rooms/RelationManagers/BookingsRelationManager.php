<?php

namespace App\Filament\Resources\Rooms\RelationManagers;

use App\Filament\Resources\Bookings\Tables\BookingsTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class BookingsRelationManager extends RelationManager
{
    protected static string $relationship = 'bookings';

    protected static ?string $title = 'الحجوزات';

    public function table(Table $table): Table
    {
        return BookingsTable::configure($table);
    }
}
