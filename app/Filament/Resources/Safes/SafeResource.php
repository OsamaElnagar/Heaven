<?php

namespace App\Filament\Resources\Safes;

use App\Filament\Resources\Safes\Pages\CreateSafe;
use App\Filament\Resources\Safes\Pages\EditSafe;
use App\Filament\Resources\Safes\Pages\ListSafes;
use App\Filament\Resources\Safes\Schemas\SafeForm;
use App\Filament\Resources\Safes\Tables\SafesTable;
use App\Models\Safe;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SafeResource extends Resource
{
    protected static ?string $model = Safe::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static \UnitEnum|string|null $navigationGroup = 'الخزينة والسندات';

    protected static ?string $navigationLabel = 'الخزائن';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'code';

    protected static ?string $modelLabel = 'خزينة';

    protected static ?string $pluralModelLabel = 'الخزائن';

    public static function form(Schema $schema): Schema
    {
        return SafeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SafesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSafes::route('/'),
            'create' => CreateSafe::route('/create'),
            'edit' => EditSafe::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['code', 'name'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return $record->name.' - '.$record->code;
    }

    public static function getGlobalSearchResultActions(Model $record): array
    {
        return [
            Action::make('edit')
                ->url(static::getUrl('edit', ['record' => $record])),
        ];
    }
}
