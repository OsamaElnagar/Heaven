<?php

namespace App\Filament\Resources\Suppliers\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SupplierInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات المورد')
                    ->components([
                        TextEntry::make('name')
                            ->label('اسم المورد'),
                        TextEntry::make('type')
                            ->label('النوع')
                            ->badge(),
                        TextEntry::make('country')
                            ->label('البلد'),
                        TextEntry::make('city')
                            ->label('المدينة')
                            ->placeholder('—'),
                    ])
                    ->columns(2),

                Section::make('معلومات الاتصال')
                    ->components([
                        TextEntry::make('contact_person')
                            ->label('الشخص المسؤول')
                            ->placeholder('—'),
                        TextEntry::make('phone')
                            ->label('رقم الهاتف')
                            ->placeholder('—'),
                        TextEntry::make('email')
                            ->label('البريد الإلكتروني')
                            ->placeholder('—'),
                    ])
                    ->columns(2),
            ]);
    }
}
