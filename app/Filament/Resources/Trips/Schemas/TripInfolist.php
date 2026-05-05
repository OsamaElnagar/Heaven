<?php

namespace App\Filament\Resources\Trips\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TripInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات الرحلة')
                    ->components([
                        TextEntry::make('name')
                            ->label('اسم الرحلة'),
                        TextEntry::make('package.name')
                            ->label('الباقة'),
                        TextEntry::make('status')
                            ->label('الحالة')
                            ->badge(),
                        TextEntry::make('airline')
                            ->label('شركة الطيران')
                            ->placeholder('-'),
                        TextEntry::make('flight_number')
                            ->label('رقم الرحلة')
                            ->placeholder('-'),
                    ])
                    ->columns(2),

                Section::make('مواعيد السفر')
                    ->components([
                        TextEntry::make('departure_at')
                            ->label('موعد المغادرة')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('return_at')
                            ->label('موعد العودة')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('departure_airport')
                            ->label('مطار المغادرة')
                            ->placeholder('-'),
                    ])
                    ->columns(2),
            ]);
    }
}
