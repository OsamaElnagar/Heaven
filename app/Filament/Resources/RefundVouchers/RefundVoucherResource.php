<?php

namespace App\Filament\Resources\RefundVouchers;

use App\Filament\Resources\RefundVouchers\Pages\CreateRefundVoucher;
use App\Filament\Resources\RefundVouchers\Pages\EditRefundVoucher;
use App\Filament\Resources\RefundVouchers\Pages\ListRefundVouchers;
use App\Filament\Resources\RefundVouchers\Pages\ViewRefundVoucher;
use App\Filament\Resources\RefundVouchers\Schemas\RefundVoucherForm;
use App\Filament\Resources\RefundVouchers\Tables\RefundVouchersTable;
use App\Models\RefundVoucher;
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

class RefundVoucherResource extends Resource
{
    protected static ?string $model = RefundVoucher::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUturnLeft;

    protected static \UnitEnum|string|null $navigationGroup = 'الخزينة والسندات';

    protected static ?string $navigationLabel = 'سندات الاسترداد';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'number';

    protected static ?string $modelLabel = 'سند استرداد';

    protected static ?string $pluralModelLabel = 'سندات الاسترداد';

    public static function form(Schema $schema): Schema
    {
        return RefundVoucherForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RefundVouchersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRefundVouchers::route('/'),
            'create' => CreateRefundVoucher::route('/create'),
            'view' => ViewRefundVoucher::route('/{record}'),
            'edit' => EditRefundVoucher::route('/{record}/edit'),
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
        return ['number', 'description', 'reference'];
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
