<?php

namespace App\Filament\Resources\Suppliers\Schemas;

use App\Enums\SupplierType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SupplierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات المورد')
                    ->components([
                        TextInput::make('name')
                            ->label('اسم المورد')
                            ->required(),
                        Select::make('type')
                            ->label('النوع')
                            ->options(SupplierType::class)
                            ->required()
                            ->native(false),
                        TextInput::make('country')
                            ->label('البلد')
                            ->default('SA'),
                        TextInput::make('city')
                            ->label('المدينة'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('معلومات الاتصال')
                    ->components([
                        TextInput::make('contact_person')
                            ->label('الشخص المسؤول'),
                        TextInput::make('phone')
                            ->label('رقم الهاتف')
                            ->tel(),
                        TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email(),
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
