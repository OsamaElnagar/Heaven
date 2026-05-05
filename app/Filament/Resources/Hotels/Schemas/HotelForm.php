<?php

namespace App\Filament\Resources\Hotels\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HotelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات الفندق')
                    ->components([
                        TextInput::make('name')
                            ->label('اسم الفندق')
                            ->required(),
                        Select::make('supplier_id')
                            ->label('المورد')
                            ->relationship('supplier', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false),
                        Select::make('city')
                            ->label('المدينة')
                            ->options([
                                'makkah' => 'مكة المكرمة',
                                'madinah' => 'المدينة المنورة',
                            ])
                            ->required()
                            ->native(false),
                        TextInput::make('stars')
                            ->label('التصنيف (نجوم)')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(5),
                        TextInput::make('distance_to_haram')
                            ->label('المسافة إلى الحرم'),
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
