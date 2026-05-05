<?php

namespace App\Filament\Resources\Packages\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PackageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات الباقة')
                    ->components([
                        TextEntry::make('name')
                            ->label('الاسم'),
                        TextEntry::make('type')
                            ->label('النوع')
                            ->badge(),
                        TextEntry::make('grade')
                            ->label('الدرجة')
                            ->badge(),
                        TextEntry::make('season_year')
                            ->label('سنة الموسم'),
                        TextEntry::make('duration_nights')
                            ->label('عدد الليالي'),
                        TextEntry::make('base_price')
                            ->label('السعر الأساسي')
                            ->money('EGP'),
                    ])
                    ->columns(2),

                Section::make('المقاعد والتواريخ')
                    ->components([
                        TextEntry::make('total_seats')
                            ->label('إجمالي المقاعد'),
                        TextEntry::make('reserved_seats')
                            ->label('المقاعد المحجوزة'),
                        TextEntry::make('departure_date')
                            ->label('تاريخ المغادرة')
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('return_date')
                            ->label('تاريخ العودة')
                            ->date()
                            ->placeholder('-'),
                        IconEntry::make('is_active')
                            ->label('نشط')
                            ->boolean(),
                    ])
                    ->columns(2),

                Section::make('تفاصيل إضافية')
                    ->components([
                        TextEntry::make('includes')
                            ->label('يشمل')
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('excludes')
                            ->label('لا يشمل')
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('notes')
                            ->label('ملاحظات')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
