<?php

namespace App\Filament\Resources\Hotels\Schemas;

use App\Filament\Resources\Suppliers\SupplierResource;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

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
                            ->label('المورد')
                            ->url(fn ($record) => $record->supplier
                                ? SupplierResource::getUrl('edit', ['record' => $record->supplier])
                                : null, true)
                            ->icon(Heroicon::ArrowUpRight)
                            ->color('primary'),
                        TextEntry::make('city.name_ar')
                            ->label('المدينة')
                            ->badge(),
                        TextEntry::make('stars')
                            ->label('التصنيف (نجوم)')
                            ->placeholder('—'),
                        TextEntry::make('distance_to_haram')
                            ->label('المسافة إلى الحرم')
                            ->placeholder('—'),
                    ])
                    ->columns(2),

                Section::make('ملاحظات')
                    ->components([
                        TextEntry::make('notes')
                            ->label('ملاحظات')
                            ->placeholder('—')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
