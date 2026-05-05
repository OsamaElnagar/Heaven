<?php

namespace App\Filament\Resources\Packages\Tables;

use App\Enums\PackageType;
use App\Filament\Resources\Packages\Actions\DuplicatePackageAction;
use App\Filament\Resources\Packages\Actions\ToggleActiveAction;
use App\Models\Package;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PackagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('النوع')
                    ->badge()
                    ->sortable(),
                TextColumn::make('grade')
                    ->label('الدرجة')
                    ->badge()
                    ->sortable(),
                TextColumn::make('season_year')
                    ->label('الموسم')
                    ->sortable(),
                TextColumn::make('base_price')
                    ->label('السعر')
                    ->money('EGP')
                    ->sortable()
                    ->summarize(Sum::make()->label('الإجمالي')->money('EGP')),
                TextColumn::make('total_seats')
                    ->label('المقاعد')
                    ->numeric()
                    ->sortable()
                    ->summarize(Sum::make()->label('الإجمالي')),
                TextColumn::make('reserved_seats')
                    ->label('المحجوز')
                    ->numeric()
                    ->sortable()
                    ->summarize(Sum::make()->label('الإجمالي')),
                IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
                TextColumn::make('departure_date')
                    ->label('المغادرة')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('النوع')
                    ->options(PackageType::class),
                SelectFilter::make('season_year')
                    ->label('الموسم')
                    ->options(fn () => Package::query()
                        ->distinct()
                        ->orderByDesc('season_year')
                        ->pluck('season_year', 'season_year')
                    ),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                ToggleActiveAction::make(),
                DuplicatePackageAction::make(),
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
