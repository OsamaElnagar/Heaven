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
                        TextInput::make('national_id')
                            ->label('الرقم القومي'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('العمولة والعقد')
                    ->components([
                        TextInput::make('commission_percentage')
                            ->label('نسبة العمولة (%)')
                            ->numeric()
                            ->default(0)
                            ->suffix('%'),
                        DatePicker::make('contract_date')
                            ->label('تاريخ العقد')
                            ->native(false),
                        Textarea::make('notes')
                            ->label('ملاحظات')
                            ->columnSpanFull(),
                        Toggle::make('is_active')
                            ->label('نشط')
                            ->default(true),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
