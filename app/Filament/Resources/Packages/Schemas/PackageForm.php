<?php

namespace App\Filament\Resources\Packages\Schemas;

use App\Enums\PackageGrade;
use App\Enums\PackageType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PackageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات الباقة')
                    ->components([
                        TextInput::make('name')
                            ->label('الاسم')
                            ->required(),
                        Select::make('type')
                            ->label('النوع')
                            ->options(PackageType::class)
                            ->required()
                            ->native(false),
                        Select::make('grade')
                            ->label('الدرجة')
                            ->options(PackageGrade::class)
                            ->required()
                            ->native(false),
                        TextInput::make('season_year')
                            ->label('سنة الموسم')
                            ->required()
                            ->numeric(),
                        TextInput::make('duration_nights')
                            ->label('عدد الليالي')
                            ->required()
                            ->numeric(),
                        TextInput::make('base_price')
                            ->label('السعر الأساسي')
                            ->required()
                            ->numeric()
                            ->prefix('ج.م'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('المقاعد والتواريخ')
                    ->components([
                        TextInput::make('total_seats')
                            ->label('إجمالي المقاعد')
                            ->required()
                            ->numeric(),
                        TextInput::make('reserved_seats')
                            ->label('المقاعد المحجوزة')
                            ->numeric()
                            ->default(0),
                        DatePicker::make('departure_date')
                            ->label('تاريخ المغادرة')
                            ->native(false),
                        DatePicker::make('return_date')
                            ->label('تاريخ العودة')
                            ->native(false),
                        Toggle::make('is_active')
                            ->label('نشط'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('تفاصيل إضافية')
                    ->components([
                        Textarea::make('includes')
                            ->label('يشمل')
                            ->columnSpanFull(),
                        Textarea::make('excludes')
                            ->label('لا يشمل')
                            ->columnSpanFull(),
                        Textarea::make('notes')
                            ->label('ملاحظات')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
