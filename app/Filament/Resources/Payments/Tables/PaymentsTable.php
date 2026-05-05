<?php

namespace App\Filament\Resources\Payments\Tables;

use App\Enums\PaymentMethod;
use App\Enums\PaymentType;
use App\Filament\Components\Filters\DateRangeFilter;
use App\Filament\Resources\Payments\Actions\PrintReceiptAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('booking.reference')
                    ->label('الحجز')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('النوع')
                    ->badge()
                    ->sortable(),
                TextColumn::make('method')
                    ->label('طريقة الدفع')
                    ->badge()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('المبلغ')
                    ->money('EGP')
                    ->sortable()
                    ->summarize(Sum::make()->label('الإجمالي')->money('EGP')),
                TextColumn::make('paid_at')
                    ->label('تاريخ الدفع')
                    ->date()
                    ->sortable(),
                TextColumn::make('reference')
                    ->label('المرجع')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('receivedBy.name')
                    ->label('استلم بواسطة')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('النوع')
                    ->options(PaymentType::class),
                SelectFilter::make('method')
                    ->label('طريقة الدفع')
                    ->options(PaymentMethod::class),
                SelectFilter::make('received_by')
                    ->label('استلم بواسطة')
                    ->relationship('receivedBy', 'name'),
                DateRangeFilter::make('paid_at'),
            ])
            ->recordActions([
                ViewAction::make(),
                PrintReceiptAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
