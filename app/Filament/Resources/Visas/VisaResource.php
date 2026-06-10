<?php

namespace App\Filament\Resources\Visas;

use App\Filament\Resources\Visas\Pages\CreateVisa;
use App\Filament\Resources\Visas\Pages\EditVisa;
use App\Filament\Resources\Visas\Pages\ListVisas;
use App\Filament\Resources\Visas\Pages\ViewVisa;
use App\Filament\Resources\Visas\Schemas\VisaForm;
use App\Filament\Resources\Visas\Schemas\VisaInfolist;
use App\Filament\Resources\Visas\Tables\VisasTable;
use App\Models\Visa;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class VisaResource extends Resource
{
    protected static ?string $model = Visa::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCheck;

    protected static \UnitEnum|string|null $navigationGroup = 'التأشيرات';

    protected static ?string $navigationLabel = 'التأشيرات';

    protected static ?string $recordTitleAttribute = 'visa_number';

    protected static ?string $modelLabel = 'تأشيرة';

    protected static ?string $pluralModelLabel = 'التأشيرات';

    public static function form(Schema $schema): Schema
    {
        return VisaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return VisaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VisasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVisas::route('/'),
            'create' => CreateVisa::route('/create'),
            'view' => ViewVisa::route('/{record}'),
            'edit' => EditVisa::route('/{record}/edit'),
        ];
    }
}
