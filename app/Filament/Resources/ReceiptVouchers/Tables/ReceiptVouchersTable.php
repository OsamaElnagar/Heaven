<?php

namespace App\Filament\Resources\ReceiptVouchers\Tables;

use App\Enums\ExpenseStatus;
use App\Filament\Components\Filters\DateRangeFilter;
use App\Filament\Resources\ReceiptVouchers\Actions\PostVoucherAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ReceiptVouchersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->copyable()
                    ->label('الرقم')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('voucher_date')
                    ->label('تاريخ السند')
                    ->date()
                    ->sortable(),
                TextColumn::make('booking.reference')
                    ->label('الحجز')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('payment_type')
                    ->label('نوع الدفعة')
                    ->badge()
                    ->placeholder('-'),
                TextColumn::make('amount')
                    ->label('المبلغ')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('payer_type')
                    ->label('نوع الدافع')
                    ->badge()
                    ->searchable(),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('safe.name')
                    ->label('الخزنة')
                    ->searchable(),
                TextColumn::make('bankAccount.bank_name')
                    ->label('الحساب البنكي')
                    ->searchable(),
                TextColumn::make('description')
                    ->label('البيان')
                    ->searchable()
                    ->limit(30),
            ])
            ->defaultSort('voucher_date', 'desc')
            ->filters([
                DateRangeFilter::make('voucher_date')
                    ->label('تاريخ السند'),
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(ExpenseStatus::class),
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
