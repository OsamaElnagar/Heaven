<?php

namespace App\Filament\Resources\Agents\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AgentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات الوكيل')
                    ->components([
                        TextInput::make('code')
                            ->label('الكود')
                            ->disabled()
                            ->hint('يتم إنشاؤه تلقائياً'),
                        TextInput::make('name')
                            ->label('الاسم')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('اسم الوكيل'),
                        TextInput::make('phone')
                            ->label('الهاتف')
                            ->tel()
                            ->maxLength(255)
                            ->placeholder('05xxxxxxxxx'),
                        TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->maxLength(255)
                            ->placeholder('example@domain.com'),
                        TextInput::make('national_id')
                            ->label('الرقم القومي')
                            ->maxLength(255)
                            ->placeholder('XXXXXXXXXXXXXXX'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('العمولة والعقد')
                    ->components([
                        TextInput::make('commission_percentage')
                            ->label('نسبة العمولة (%)')
                            ->numeric()
                            ->default(0)
                            ->suffix('%')
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->placeholder('0.00'),
                        DatePicker::make('contract_date')
                            ->label('تاريخ العقد')
                            ->native(false),
                        Textarea::make('notes')
                            ->label('ملاحظات')
                            ->columnSpanFull()
                            ->placeholder('أي ملاحظات إضافية...'),
                        Toggle::make('is_active')
                            ->label('نشط')
                            ->inline(false)
                            ->default(true),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
