<?php

namespace App\Filament\Resources\Rooms\Schemas;

use App\Enums\RoomType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RoomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات الغرفة')
                    ->components([
                        Select::make('hotel_id')
                            ->label('الفندق')
                            ->relationship('hotel', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false),
                        Select::make('trip_id')
                            ->label('الرحلة')
                            ->relationship('trip', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false),
                        TextInput::make('room_number')
                            ->label('رقم الغرفة'),
                        Select::make('type')
                            ->label('النوع')
                            ->options(RoomType::class)
                            ->required()
                            ->native(false),
                        TextInput::make('capacity')
                            ->label('السعة')
                            ->required()
                            ->numeric()
                            ->minValue(1),
                        TextInput::make('occupied')
                            ->label('المشغول')
                            ->numeric()
                            ->default(0),
                        TextInput::make('price_per_person')
                            ->label('سعر الفرد')
                            ->numeric()
                            ->prefix('ج.م'),
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
