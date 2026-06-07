<?php

namespace App\Filament\Resources\PaymentVouchers;

use App\Filament\Resources\PaymentVouchers\Pages\CreatePaymentVoucher;
use App\Filament\Resources\PaymentVouchers\Pages\EditPaymentVoucher;
use App\Filament\Resources\PaymentVouchers\Pages\ListPaymentVouchers;
use App\Filament\Resources\PaymentVouchers\Pages\ViewPaymentVoucher;
use App\Filament\Resources\PaymentVouchers\Schemas\PaymentVoucherForm;
use App\Filament\Resources\PaymentVouchers\Tables\PaymentVouchersTable;
use App\Models\PaymentVoucher;
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

class PaymentVoucherResource extends Resource
{
    protected static ?string $model = PaymentVoucher::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUpOnSquare;

    protected static \UnitEnum|string|null $navigationGroup = 'الخزينة والسندات';

    protected static ?string $navigationLabel = 'سندات الصرف';

    protected static ?string $recordTitleAttribute = 'number';

    protected static ?string $modelLabel = 'سند صرف';

    protected static ?string $pluralModelLabel = 'سندات الصرف';

    public static function form(Schema $schema): Schema
    {
        return PaymentVoucherForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PaymentVouchersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPaymentVouchers::route('/'),
            'create' => CreatePaymentVoucher::route('/create'),
            'view' => ViewPaymentVoucher::route('/{record}'),
            'edit' => EditPaymentVoucher::route('/{record}/edit'),
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
        return ['number', 'description', 'payee_name'];
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
