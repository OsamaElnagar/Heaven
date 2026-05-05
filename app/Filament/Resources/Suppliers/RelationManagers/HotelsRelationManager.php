<?php

namespace App\Filament\Resources\Suppliers\RelationManagers;

use App\Filament\Resources\Hotels\Schemas\HotelForm;
use App\Filament\Resources\Hotels\Tables\HotelsTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class HotelsRelationManager extends RelationManager
{
    protected static string $relationship = 'hotels';

    protected static ?string $title = 'الفنادق';

    public function form(Schema $schema): Schema
    {
        return HotelForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return HotelsTable::configure($table);
    }
}
