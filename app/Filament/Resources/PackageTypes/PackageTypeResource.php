<?php

namespace App\Filament\Resources\PackageTypes;

use App\Filament\Resources\PackageTypes\Pages\CreatePackageType;
use App\Filament\Resources\PackageTypes\Pages\EditPackageType;
use App\Filament\Resources\PackageTypes\Pages\ListPackageTypes;
use App\Filament\Resources\PackageTypes\Schemas\PackageTypeForm;
use App\Filament\Resources\PackageTypes\Tables\PackageTypesTable;
use App\Models\PackageType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PackageTypeResource extends Resource
{
    protected static ?string $model = PackageType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static \UnitEnum|string|null $navigationGroup = 'الإعدادات';

    protected static ?string $navigationLabel = 'أنواع الباقات';

    protected static ?string $recordTitleAttribute = 'name_ar';

    protected static ?string $modelLabel = 'نوع الباقة';

    protected static ?string $pluralModelLabel = 'أنواع الباقات';

    public static function form(Schema $schema): Schema
    {
        return PackageTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PackageTypesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPackageTypes::route('/'),
            'create' => CreatePackageType::route('/create'),
            'edit' => EditPackageType::route('/{record}/edit'),
        ];
    }
}
