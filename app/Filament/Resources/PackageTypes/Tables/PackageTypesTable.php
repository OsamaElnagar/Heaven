<?php

namespace App\Filament\Resources\PackageTypes\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PackageTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name_ar')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('color')
                    ->label('اللون')
                    ->badge()
                    ->color(fn (string $state): string => $state),
                IconColumn::make('is_religious')
                    ->label('ديني')
                    ->boolean(),
                TextColumn::make('duration_nights_min')
                    ->label('الحد الأدنى (ليالي)')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('duration_nights_max')
                    ->label('الحد الأقصى (ليالي)')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('packages_count')
                    ->label('عدد الباقات')
                    ->counts('packages')
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('name_ar');
    }
}
