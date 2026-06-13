<?php

namespace App\Filament\Resources\Rooms\Schemas;

use App\Filament\Resources\Hotels\HotelResource;
use App\Filament\Resources\Trips\TripResource;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class RoomInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات الغرفة')
                    ->components([
                        TextEntry::make('hotel.name')
                            ->label('الفندق')
                            ->url(fn ($record) => $record->hotel
                                ? HotelResource::getUrl('edit', ['record' => $record->hotel])
                                : null, true)
                            ->icon(Heroicon::ArrowUpRight)
                            ->color('primary'),
                        TextEntry::make('trip.name')
                            ->label('الرحلة')
                            ->url(fn ($record) => $record->trip
                                ? TripResource::getUrl('edit', ['record' => $record->trip])
                                : null, true)
                            ->icon(Heroicon::ArrowUpRight)
                            ->color('primary'),
                        TextEntry::make('room_number')
                            ->label('رقم الغرفة')
                            ->placeholder('—'),
                        TextEntry::make('type')
                            ->label('النوع')
                            ->badge(),
                        TextEntry::make('capacity')
                            ->label('السعة'),
                        TextEntry::make('occupied')
                            ->label('المشغول'),
                        TextEntry::make('available')
                            ->label('المتاح')
                            ->state(fn ($record): int => max($record->capacity - $record->occupied, 0)),
                        TextEntry::make('price_per_person')
                            ->label('سعر الفرد')
                            ->money('EGP')
                            ->placeholder('—'),
                    ])
                    ->columns(2),
            ]);
    }
}
