<?php

namespace App\Filament\Resources\Trips\Schemas;

use App\Enums\TripStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TripForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات الرحلة')
                    ->components([
                        TextInput::make('name')
                            ->label('اسم الرحلة')
                            ->required(),
                        Select::make('package_id')
                            ->label('الباقة')
                            ->relationship('package', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false),
                        Select::make('status')
                            ->label('الحالة')
                            ->options(TripStatus::class)
                            ->required()
                            ->native(false),
                        TextInput::make('airline')
                            ->label('شركة الطيران'),
                        TextInput::make('flight_number')
                            ->label('رقم الرحلة'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('مواعيد السفر')
                    ->components([
                        DateTimePicker::make('departure_at')
                            ->label('موعد المغادرة')
                            ->native(false),
                        DateTimePicker::make('return_at')
                            ->label('موعد العودة')
                            ->native(false),
                        TextInput::make('departure_airport')
                            ->label('مطار المغادرة'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('ملاحظات')
                    ->components([
                        Textarea::make('notes')
                            ->label('ملاحظات')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
