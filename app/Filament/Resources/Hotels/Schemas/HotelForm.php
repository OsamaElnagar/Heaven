<?php

namespace App\Filament\Resources\Hotels\Schemas;

use App\Filament\Resources\Suppliers\Schemas\SupplierForm;
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
                            ->relationship('supplier', 'name', modifyQueryUsing: fn ($query) => $query->where('is_active', true))
                            ->createOptionForm(fn ($schema) => SupplierForm::configure($schema))
                            ->editOptionForm(fn ($schema) => SupplierForm::configure($schema))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false),
                        Select::make('city_id')
                            ->label('المدينة')
                            ->relationship('city', 'name_ar')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false),
                        Select::make('stars')
                            ->label('التصنيف (نجوم)')
                            ->options([
                                1 => 'نجمة واحدة',
                                2 => 'نجمتان',
                                3 => 'ثلاث نجوم',
                                4 => 'أربع نجوم',
                                5 => 'خمس نجوم',
                            ])
                            ->native(false),
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
