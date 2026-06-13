<?php

namespace App\Filament\Resources\Trips\Tables;

use App\Enums\TripStatus;
use App\Filament\Components\Filters\DateRangeFilter;
use App\Filament\Resources\Packages\PackageResource;
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
                    ->url(fn ($record) => PackageResource::getUrl('edit', ['record' => $record->package]))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->sortable(),
                TextColumn::make('departure_at')
                    ->label('موعد المغادرة')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('airline')
                    ->label('شركة الطيران')
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->toggleable(),
                TextColumn::make('departure_airport')
                    ->label('مطار المغادرة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'CAI' => 'info',
                        'RUH' => 'success',
                        'MED' => 'warning',
                        default => 'gray',
                    })
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('flight_number')
                    ->label('رقم الرحلة')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->copyable(),
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
                    ->relationship('package', 'name')
                    ->searchable()
                    ->preload(),
                DateRangeFilter::make('departure_at')
                    ->label('موعد المغادرة'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('departure_at', 'desc');
    }
}
