<?php

namespace App\Filament\Resources\Trips;

use App\Filament\Resources\Trips\Pages\CreateTrip;
use App\Filament\Resources\Trips\Pages\EditTrip;
use App\Filament\Resources\Trips\Pages\ListTrips;
use App\Filament\Resources\Trips\Pages\TripDashboardPage;
use App\Filament\Resources\Trips\Pages\TripManifestPage;
use App\Filament\Resources\Trips\Pages\TripRoomingPage;
use App\Filament\Resources\Trips\Pages\ViewTrip;
use App\Filament\Resources\Trips\RelationManagers\BookingsRelationManager;
use App\Filament\Resources\Trips\RelationManagers\ExpensesRelationManager;
use App\Filament\Resources\Trips\RelationManagers\RoomsRelationManager;
use App\Filament\Resources\Trips\Schemas\TripForm;
use App\Filament\Resources\Trips\Schemas\TripInfolist;
use App\Filament\Resources\Trips\Tables\TripsTable;
use App\Models\Trip;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TripResource extends Resource
{
    protected static ?string $model = Trip::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    protected static \UnitEnum|string|null $navigationGroup = 'الموردون والفنادق والرحلات';

    protected static ?string $navigationLabel = 'الرحلات';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'رحلة';

    protected static ?string $pluralModelLabel = 'الرحلات';

    public static function form(Schema $schema): Schema
    {
        return TripForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TripInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TripsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            BookingsRelationManager::class,
            RoomsRelationManager::class,
            ExpensesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrips::route('/'),
            'create' => CreateTrip::route('/create'),
            'view' => ViewTrip::route('/{record}'),
            'edit' => EditTrip::route('/{record}/edit'),
            'dashboard' => TripDashboardPage::route('/{record}/dashboard'),
            'manifest' => TripManifestPage::route('/{record}/manifest'),
            'rooming' => TripRoomingPage::route('/{record}/rooming'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'flight_number'];
    }
}
