<?php

namespace App\Filament\Resources\ReceiptVouchers;

use App\Filament\Resources\ReceiptVouchers\Pages\CreateReceiptVoucher;
use App\Filament\Resources\ReceiptVouchers\Pages\EditReceiptVoucher;
use App\Filament\Resources\ReceiptVouchers\Pages\ListReceiptVouchers;
use App\Filament\Resources\ReceiptVouchers\Pages\ViewReceiptVoucher;
use App\Filament\Resources\ReceiptVouchers\Schemas\ReceiptVoucherForm;
use App\Filament\Resources\ReceiptVouchers\Tables\ReceiptVouchersTable;
use App\Models\ReceiptVoucher;
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

class ReceiptVoucherResource extends Resource
{
    protected static ?string $model = ReceiptVoucher::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowDownOnSquare;

    protected static \UnitEnum|string|null $navigationGroup = 'الخزينة والسندات';

    protected static ?string $navigationLabel = 'سندات القبض';

    protected static ?int $navigationSort = 0;

    protected static ?string $recordTitleAttribute = 'number';

    protected static ?string $modelLabel = 'سند قبض';

    protected static ?string $pluralModelLabel = 'سندات القبض';

    public static function form(Schema $schema): Schema
    {
        return ReceiptVoucherForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReceiptVouchersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReceiptVouchers::route('/'),
            'create' => CreateReceiptVoucher::route('/create'),
            'view' => ViewReceiptVoucher::route('/{record}'),
            'edit' => EditReceiptVoucher::route('/{record}/edit'),
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
        return ['number', 'description', 'payer_name'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return $record->number.' - '.$record->description;
    }

    public static function getGlobalSearchResultActions(Model $record): array
    {
        return [
            Action::make('edit')
                ->url(static::getUrl('edit', ['record' => $record])),
        ];
    }
}
