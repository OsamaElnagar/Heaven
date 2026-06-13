<?php

namespace App\Filament\Resources\PackageTypes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

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
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),
                        TextInput::make('name_ar')
                            ->label('الاسم (عربي)')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('slug')
                            ->label('الرابط')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->hint('يتم إنشاؤه تلقائياً من الاسم الإنجليزي'),
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
                            ->searchable()
                            ->native(false),
                        TextInput::make('icon')
                            ->label('الأيقونة')
                            ->placeholder('heroicon-o-star')
                            ->maxLength(255)
                            ->hint('اسم أيقونة Heroicons'),
                        Toggle::make('is_religious')
                            ->label('نوع ديني')
                            ->inline(false),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('مدة الرحلة')
                    ->description('اختياري - يحدد مدد الرحلات المسموحة لهذا النوع')
                    ->components([
                        TextInput::make('duration_nights_min')
                            ->label('الحد الأدنى (ليالي)')
                            ->integer()
                            ->minValue(0)
                            ->nullable()
                            ->placeholder('0'),
                        TextInput::make('duration_nights_max')
                            ->label('الحد الأقصى (ليالي)')
                            ->integer()
                            ->minValue(0)
                            ->nullable()
                            ->placeholder('0'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
