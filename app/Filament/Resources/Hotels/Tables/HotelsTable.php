<?php

namespace App\Filament\Resources\Hotels\Tables;

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

class HotelsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('اسم الفندق')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('city.name_ar')
                    ->label('المدينة')
                    ->badge()
                    ->sortable(),
                TextColumn::make('stars')
                    ->label('النجوم')
                    ->numeric()
                    ->sortable()
                    ->summarize(Sum::make()->label('الإجمالي')),
                TextColumn::make('supplier.name')
                    ->label('المورد')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('distance_to_haram')
                    ->label('المسافة إلى الحرم')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('city_id')
                    ->label('المدينة')
                    ->relationship('city', 'name_ar'),
                SelectFilter::make('stars')
                    ->label('التصنيف')
                    ->options([
                        1 => 'نجمة واحدة',
                        2 => 'نجمتان',
                        3 => 'ثلاث نجوم',
                        4 => 'أربع نجوم',
                        5 => 'خمس نجوم',
                    ]),
                SelectFilter::make('supplier_id')
                    ->label('المورد')
                    ->relationship('supplier', 'name', modifyQueryUsing: fn ($query) => $query->where('is_active', true)),
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
            ->defaultSort('name');
    }
}
