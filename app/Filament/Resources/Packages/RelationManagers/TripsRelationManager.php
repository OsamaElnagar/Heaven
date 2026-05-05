<?php

namespace App\Filament\Resources\Packages\RelationManagers;

use App\Filament\Resources\Trips\Schemas\TripForm;
use App\Filament\Resources\Trips\Tables\TripsTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class TripsRelationManager extends RelationManager
{
    protected static string $relationship = 'trips';

    protected static ?string $title = 'الرحلات';

    public function form(Schema $schema): Schema
    {
        return TripForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return TripsTable::configure($table);
    }
}
