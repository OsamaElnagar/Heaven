<?php

namespace App\Filament\Resources\EmployeeAdvances;

use App\Filament\Resources\EmployeeAdvances\Pages\CreateEmployeeAdvance;
use App\Filament\Resources\EmployeeAdvances\Pages\EditEmployeeAdvance;
use App\Filament\Resources\EmployeeAdvances\Pages\ListEmployeeAdvances;
use App\Filament\Resources\EmployeeAdvances\Pages\ViewEmployeeAdvance;
use App\Filament\Resources\EmployeeAdvances\Schemas\EmployeeAdvanceForm;
use App\Filament\Resources\EmployeeAdvances\Tables\EmployeeAdvancesTable;
use App\Models\EmployeeAdvance;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class EmployeeAdvanceResource extends Resource
{
    protected static ?string $model = EmployeeAdvance::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|\UnitEnum|null $navigationGroup = 'الموارد البشرية';

    protected static ?string $navigationLabel = 'سلف الموظفين';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'code';

    protected static ?string $modelLabel = 'سلفة موظف';

    protected static ?string $pluralModelLabel = 'سلف الموظفين';

    public static function form(Schema $schema): Schema
    {
        return EmployeeAdvanceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmployeeAdvancesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmployeeAdvances::route('/'),
            'create' => CreateEmployeeAdvance::route('/create'),
            'view' => ViewEmployeeAdvance::route('/{record}'),
            'edit' => EditEmployeeAdvance::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['code', 'notes'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return $record->code.' - '.$record->notes;
    }

    public static function getGlobalSearchResultActions(Model $record): array
    {
        return [
            Action::make('edit')
                ->url(static::getUrl('edit', ['record' => $record])),
        ];
    }
}
