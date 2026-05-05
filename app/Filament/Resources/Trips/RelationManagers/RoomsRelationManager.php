<?php

namespace App\Filament\Resources\Trips\RelationManagers;

use App\Filament\Resources\Rooms\Schemas\RoomForm;
use App\Filament\Resources\Rooms\Tables\RoomsTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class RoomsRelationManager extends RelationManager
{
    protected static string $relationship = 'rooms';

    protected static ?string $title = 'الغرف';

    public function form(Schema $schema): Schema
    {
        return RoomForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return RoomsTable::configure($table);
    }
}
