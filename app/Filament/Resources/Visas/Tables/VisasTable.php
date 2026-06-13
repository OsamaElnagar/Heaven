<?php

namespace App\Filament\Resources\Visas\Tables;

use App\Enums\VisaStatus;
use App\Filament\Components\Filters\DateRangeFilter;
use App\Filament\Resources\Visas\Actions\BulkSubmitAction;
use App\Filament\Resources\Visas\Actions\MarkApprovedAction;
use App\Filament\Resources\Visas\Actions\MarkRejectedAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class VisasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('booking.reference')
                    ->label('الحجز')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('booking.client.name')
                    ->label('العميل')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->sortable(),
                TextColumn::make('visa_number')
                    ->label('رقم التأشيرة')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('applied_at')
                    ->label('تاريخ التقديم')
                    ->date()
                    ->sortable(),
                TextColumn::make('approved_at')
                    ->label('تاريخ الموافقة')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('expiry_date')
                    ->label('تاريخ الانتهاء')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(VisaStatus::class),
                SelectFilter::make('booking.trip_id')
                    ->label('الرحلة')
                    ->relationship('booking.trip', 'name'),
                DateRangeFilter::make('applied_at')
                    ->label('تاريخ التقديم'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                MarkApprovedAction::make(),
                MarkRejectedAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkSubmitAction::make(),
                ]),
            ])
            ->defaultSort('applied_at', 'desc');
    }
}
