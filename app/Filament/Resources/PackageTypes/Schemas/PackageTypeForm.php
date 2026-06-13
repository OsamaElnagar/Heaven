<?php

namespace App\Filament\Resources\PackageTypes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PackageTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات النوع')
                    ->components([
                        TextInput::make('name')
                            ->label('الاسم (إنجليزي)')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('name_ar')
                            ->label('الاسم (عربي)')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('slug')
                            ->label('الرابط')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Select::make('color')
                            ->label('اللون')
                            ->options([
                                'gray' => 'رمادي',
                                'warning' => 'أصفر',
                                'success' => 'أخضر',
                                'danger' => 'أحمر',
                                'info' => 'أزرق',
                                'primary' => 'أساسي',
                                'secondary' => 'ثانوي',
                            ])
                            ->default('gray')
                            ->native(false),
                        TextInput::make('icon')
                            ->label('الأيقونة')
                            ->placeholder('heroicon-o-star')
                            ->maxLength(255),
                        Toggle::make('is_religious')
                            ->label('نوع ديني'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('مدة الرحلة')
                    ->components([
                        TextInput::make('duration_nights_min')
                            ->label('الحد الأدنى (ليالي)')
                            ->numeric()
                            ->nullable(),
                        TextInput::make('duration_nights_max')
                            ->label('الحد الأقصى (ليالي)')
                            ->numeric()
                            ->nullable(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
