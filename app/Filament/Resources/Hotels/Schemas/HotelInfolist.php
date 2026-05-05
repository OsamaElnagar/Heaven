<?php

namespace App\Filament\Resources\Hotels\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HotelInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات الفندق')
                    ->components([
                        TextEntry::make('name')
                            ->label('اسم الفندق'),
                        TextEntry::make('supplier.name')
                            ->label('المورد'),
                        TextEntry::make('city')
                            ->label('المدينة')
                            ->badge(),
                        TextEntry::make('stars')
                            ->label('التصنيف (نجوم)')
                            ->placeholder('-'),
                        TextEntry::make('distance_to_haram')
                            ->label('المسافة إلى الحرم')
                            ->placeholder('-'),
                    ])
                    ->columns(2),
            ]);
    }
}
