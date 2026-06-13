<?php

namespace App\Filament\Resources\Branches\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BranchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات الفرع')
                    ->components([
                        TextInput::make('code')
                            ->label('الكود')
                            ->disabled(),
                        TextInput::make('name')
                            ->label('الاسم')
                            ->required(),
                        TextInput::make('phone')
                            ->label('الهاتف')
                            ->tel(),
                        TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email(),
                        Select::make('city_id')
                            ->label('المدينة')
                            ->relationship('city', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false),
                        Textarea::make('address')
                            ->label('العنوان')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('معلومات الإدارة والعمولات')
                    ->components([
                        TextInput::make('manager_name')
                            ->label('اسم المدير'),
                        TextInput::make('manager_phone')
                            ->label('هاتف المدير')
                            ->tel(),
                        TextInput::make('commission_percentage')
                            ->label('نسبة العمولة (%)')
                            ->numeric()
                            ->default(0)
                            ->suffix('%'),
                        Toggle::make('is_active')
                            ->label('نشط')
                            ->default(true),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
