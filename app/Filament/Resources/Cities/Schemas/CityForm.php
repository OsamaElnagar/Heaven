<?php

namespace App\Filament\Resources\Cities\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات المدينة')
                    ->components([
                        TextInput::make('name')
                            ->label('الاسم (إنجليزي)')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('مثال: Cairo'),
                        TextInput::make('name_ar')
                            ->label('الاسم (عربي)')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('مثال: القاهرة'),
                        TextInput::make('country')
                            ->label('الدولة')
                            ->maxLength(255)
                            ->placeholder('مثال: مصر'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
