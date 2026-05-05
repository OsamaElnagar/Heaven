<?php

namespace App\Filament\Resources\Hotels\RelationManagers;

use App\Filament\Resources\Packages\Tables\PackagesTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class PackagesRelationManager extends RelationManager
{
    protected static string $relationship = 'packages';

    protected static ?string $title = 'الباقات';

    public function table(Table $table): Table
    {
        return PackagesTable::configure($table);
    }
}
