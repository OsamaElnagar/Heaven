<?php

namespace App\Filament\Resources\Expenses\Tables;

use App\Enums\PaymentMethod;
use App\Filament\Components\Filters\DateRangeFilter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ExpensesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('description')
                    ->label('الوصف')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category')
                    ->label('الفئة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'office' => 'info',
                        'marketing' => 'success',
                        'transport' => 'warning',
                        'hotel_cost' => 'danger',
                        'airline_cost' => 'primary',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('المبلغ')
                    ->money('EGP')
                    ->sortable()
                    ->summarize(Sum::make()->label('الإجمالي')->money('EGP')),
                TextColumn::make('payment_method')
                    ->label('طريقة الدفع')
                    ->badge()
                    ->sortable(),
                TextColumn::make('paid_at')
                    ->label('تاريخ الدفع')
                    ->date()
                    ->sortable(),
                TextColumn::make('trip.name')
                    ->label('الرحلة')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('paidBy.name')
                    ->label('مدفوع بواسطة')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('—'),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('الفئة')
                    ->options([
                        'office' => 'مكتب',
                        'marketing' => 'تسويق',
                        'transport' => 'نقل',
                        'hotel_cost' => 'تكلفة فندق',
                        'airline_cost' => 'تكلفة طيران',
                        'other' => 'أخرى',
                    ]),
                SelectFilter::make('payment_method')
                    ->label('طريقة الدفع')
                    ->options(PaymentMethod::class),
                SelectFilter::make('trip_id')
                    ->label('الرحلة')
                    ->relationship('trip', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('paid_by')
                    ->label('مدفوع بواسطة')
                    ->relationship('paidBy', 'name')
                    ->searchable()
                    ->preload(),
                DateRangeFilter::make('paid_at'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
