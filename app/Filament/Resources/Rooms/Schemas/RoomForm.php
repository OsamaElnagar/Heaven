<?php

namespace App\Filament\Resources\Rooms\Schemas;

use App\Enums\RoomType;
use App\Filament\Resources\Hotels\Schemas\HotelForm;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
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
                            ->createOptionForm(fn ($schema) => HotelForm::configure($schema))
                            ->editOptionForm(fn ($schema) => HotelForm::configure($schema))
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
                            ->label('رقم الغرفة')
                            ->required(),
                        Select::make('type')
                            ->label('النوع')
                            ->options(RoomType::class)
                            ->required()
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function (Set $set, RoomType|string|null $state) {
                                $capacity = match ($state instanceof RoomType ? $state : RoomType::tryFrom($state)) {
                                    RoomType::SINGLE => 1,
                                    RoomType::DOUBLE => 2,
                                    RoomType::TRIPLE => 3,
                                    RoomType::QUAD => 4,
                                    RoomType::QUINT => 5,
                                    RoomType::SEXTUPLE => 6,
                                    default => null,
                                };

                                if ($capacity) {
                                    $set('capacity', $capacity);
                                }
                            }),
                        TextInput::make('capacity')
                            ->label('السعة')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                static::updateAvailable($set, $get);
                            }),
                        TextInput::make('occupied')
                            ->label('المشغول')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(false)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                static::updateAvailable($set, $get);
                            }),
                        TextInput::make('available')
                            ->label('المتاح')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false),
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

    protected static function updateAvailable(Set $set, Get $get): void
    {
        $capacity = (int) ($get('capacity') ?? 0);
        $occupied = (int) ($get('occupied') ?? 0);

        $set('available', max($capacity - $occupied, 0));
    }
}
