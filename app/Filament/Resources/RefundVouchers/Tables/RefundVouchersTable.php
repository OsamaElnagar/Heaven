<?php

namespace App\Filament\Resources\RefundVouchers\Tables;

use App\Enums\ExpenseStatus;
use App\Enums\RefundPartyType;
use App\Filament\Components\Filters\DateRangeFilter;
use App\Filament\Resources\Bookings\BookingResource;
use App\Filament\Resources\Clients\ClientResource;
use App\Filament\Resources\RefundVouchers\Actions\PostVoucherAction;
use App\Filament\Resources\Suppliers\SupplierResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class RefundVouchersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->copyable()
                    ->label('الرقم')
                    ->searchable(),
                TextColumn::make('voucher_date')
                    ->label('تاريخ السند')
                    ->date()
                    ->sortable(),
                TextColumn::make('party_type')
                    ->label('النوع')
                    ->badge()
                    ->formatStateUsing(fn (RefundPartyType $state): string => $state->getLabel())
                    ->color(fn (RefundPartyType $state): string|array|null => $state->getColor())
                    ->searchable(),
                TextColumn::make('party_name')
                    ->label('الطرف')
                    ->getStateUsing(function ($record): ?string {
                        if ($record->party_type === RefundPartyType::CLIENT) {
                            return $record->client?->name;
                        }
                        if ($record->party_type === RefundPartyType::SUPPLIER) {
                            return $record->supplier?->name;
                        }

                        return null;
                    })
                    ->searchable(query: function ($query, string $search) {
                        return $query->where(function ($q) use ($search) {
                            $q->whereHas('client', fn ($sub) => $sub->where('name', 'like', "%{$search}%"))
                                ->orWhereHas('supplier', fn ($sub) => $sub->where('name', 'like', "%{$search}%"));
                        });
                    })
                    ->url(fn ($record) => match ($record->party_type) {
                        RefundPartyType::CLIENT => ClientResource::getUrl('edit', ['record' => $record->client_id]),
                        RefundPartyType::SUPPLIER => SupplierResource::getUrl('edit', ['record' => $record->supplier_id]),
                        default => null,
                    }),
                TextColumn::make('amount')
                    ->label('المبلغ')
                    ->money('EGP')
                    ->sortable()
                    ->summarize(Sum::make()->label('الإجمالي')->money('EGP')),
                TextColumn::make('payment_method')
                    ->label('طريقة الدفع')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->getLabel()),
                TextColumn::make('safe.name')
                    ->label('الخزنة')
                    ->placeholder('—'),
                TextColumn::make('bankAccount.bank_name')
                    ->label('الحساب البنكي')
                    ->placeholder('—'),
                TextColumn::make('booking.reference')
                    ->label('الحجز')
                    ->placeholder('—')
                    ->url(fn ($record) => $record->booking_id
                        ? BookingResource::getUrl('edit', ['record' => $record->booking_id])
                        : null),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label('البيان')
                    ->searchable()
                    ->limit(50),
            ])
            ->defaultSort('voucher_date', 'desc')
            ->filters([
                DateRangeFilter::make('voucher_date')
                    ->label('تاريخ السند'),
                SelectFilter::make('party_type')
                    ->label('نوع الطرف')
                    ->options(RefundPartyType::class),
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(ExpenseStatus::class),
                SelectFilter::make('client_id')
                    ->label('العميل')
                    ->relationship('client', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('supplier_id')
                    ->label('المورد')
                    ->relationship('supplier', 'name')
                    ->searchable()
                    ->preload(),
                TrashedFilter::make(),
            ])
            ->recordActions([
                PostVoucherAction::make(),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
