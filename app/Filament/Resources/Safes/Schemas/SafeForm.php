<?php

namespace App\Filament\Resources\Safes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SafeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('الرقم')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('name')
                    ->label('الاسم')
                    ->required()
                    ->maxLength(255),
                Select::make('account_id')
                    ->label('حساب الأستاذ')
                    ->relationship('account', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('responsible_employee_id')
                    ->label('المسؤول')
                    ->relationship('responsibleEmployee', 'name')
                    ->searchable()
                    ->preload(),
                Toggle::make('is_active')
                    ->label('نشط')
                    ->default(true)
                    ->required(),
                Textarea::make('notes')
                    ->label('ملاحظات')
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }
}
