<?php

namespace App\Filament\Resources\Cities\Tables;

use App\Filament\Resources\Hotels\HotelResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name_ar')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('الاسم (إنجليزي)')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('country')
                    ->label('الدولة')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('hotels_count')
                    ->label('عدد الفنادق')
                    ->counts('hotels')
                    ->sortable()
                    ->url(fn ($record) => $record->hotels_count > 0
                        ? HotelResource::getUrl('index', ['filters[city_id][value]' => $record->id])
                        : null, true)
                    ->icon(Heroicon::ArrowUpRight)
                    ->color('primary'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('name_ar');
    }
}
