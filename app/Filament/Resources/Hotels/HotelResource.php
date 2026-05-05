<?php

namespace App\Filament\Resources\Hotels;

use App\Filament\Resources\Hotels\Pages\CreateHotel;
use App\Filament\Resources\Hotels\Pages\EditHotel;
use App\Filament\Resources\Hotels\Pages\ListHotels;
use App\Filament\Resources\Hotels\Pages\ViewHotel;
use App\Filament\Resources\Hotels\RelationManagers\PackagesRelationManager;
use App\Filament\Resources\Hotels\RelationManagers\RoomsRelationManager;
use App\Filament\Resources\Hotels\Schemas\HotelForm;
use App\Filament\Resources\Hotels\Schemas\HotelInfolist;
use App\Filament\Resources\Hotels\Tables\HotelsTable;
use App\Models\Hotel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class HotelResource extends Resource
{
    protected static ?string $model = Hotel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;

    protected static \UnitEnum|string|null $navigationGroup = 'الموردون والفنادق';

    protected static ?string $navigationLabel = 'الفنادق';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'فندق';

    protected static ?string $pluralModelLabel = 'الفنادق';

    public static function form(Schema $schema): Schema
    {
        return HotelForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return HotelInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HotelsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            PackagesRelationManager::class,
            RoomsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHotels::route('/'),
            'create' => CreateHotel::route('/create'),
            'view' => ViewHotel::route('/{record}'),
            'edit' => EditHotel::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'city'];
    }
}
