<?php

namespace App\Filament\Resources\Trips\Tables;

use App\Enums\TripStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TripsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('اسم الرحلة')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('package.name')
                    ->label('الباقة')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->sortable(),
                TextColumn::make('airline')
                    ->label('شركة الطيران')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('flight_number')
                    ->label('رقم الرحلة')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('departure_at')
                    ->label('موعد المغادرة')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('return_at')
                    ->label('موعد العودة')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(TripStatus::class),
                SelectFilter::make('package_id')
                    ->label('الباقة')
                    ->relationship('package', 'name'),
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
