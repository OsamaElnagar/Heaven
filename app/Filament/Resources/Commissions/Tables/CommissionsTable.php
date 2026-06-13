<?php

namespace App\Filament\Resources\Commissions\Tables;

use App\Enums\CommissionStatus;
use App\Enums\CommissionType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CommissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('booking.reference')
                    ->label('الحجز')
                    ->searchable(),
                TextColumn::make('branch.name')
                    ->label('الفرع')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('agent.name')
                    ->label('الوكيل')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('commission_type')
                    ->label('النوع')
                    ->badge()
                    ->searchable(),
                TextColumn::make('commission_rate')
                    ->label('النسبة')
                    ->suffix('%')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('المبلغ')
                    ->money('EGP')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->sortable(),
                TextColumn::make('paid_at')
                    ->label('تاريخ الدفع')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('paymentVoucher.number')
                    ->label('سند الصرف')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(CommissionStatus::class),
                SelectFilter::make('commission_type')
                    ->label('النوع')
                    ->options(CommissionType::class),
                TrashedFilter::make(),
            ])
            ->recordActions([
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
