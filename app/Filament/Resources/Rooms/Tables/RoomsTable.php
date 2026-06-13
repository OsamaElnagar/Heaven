<?php

namespace App\Filament\Resources\Rooms\Tables;

use App\Enums\RoomType;
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

class RoomsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('room_number')
                    ->label('رقم الغرفة')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                TextColumn::make('hotel.name')
                    ->label('الفندق')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('trip.name')
                    ->label('الرحلة')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('type')
                    ->label('النوع')
                    ->badge()
                    ->sortable(),
                TextColumn::make('capacity')
                    ->label('السعة')
                    ->numeric()
                    ->sortable()
                    ->summarize(Sum::make()->label('الإجمالي')),
                TextColumn::make('occupied')
                    ->label('المشغول')
                    ->numeric()
                    ->sortable()
                    ->summarize(Sum::make()->label('الإجمالي')),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('النوع')
                    ->options(RoomType::class),
                SelectFilter::make('hotel_id')
                    ->label('الفندق')
                    ->relationship('hotel', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('trip_id')
                    ->label('الرحلة')
                    ->relationship('trip', 'name')
                    ->searchable()
                    ->preload(),
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
            ]);
    }
}
