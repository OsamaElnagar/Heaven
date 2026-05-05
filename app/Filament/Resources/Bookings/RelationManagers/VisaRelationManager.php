<?php

namespace App\Filament\Resources\Bookings\RelationManagers;

use App\Filament\Resources\Visas\Schemas\VisaForm;
use App\Filament\Resources\Visas\Tables\VisasTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class VisaRelationManager extends RelationManager
{
    protected static string $relationship = 'visa';

    protected static ?string $title = 'التأشيرة';

    public function form(Schema $schema): Schema
    {
        return VisaForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return VisasTable::configure($table);
    }
}
