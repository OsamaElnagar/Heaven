<?php

namespace App\Filament\Resources\BankAccounts\Schemas;

use App\Models\Account;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BankAccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('bank_name')
                    ->label('اسم البنك')
                    ->required()
                    ->maxLength(255),
                TextInput::make('branch')
                    ->label('الفرع')
                    ->maxLength(255),
                TextInput::make('account_number')
                    ->label('رقم الحساب')
                    ->required()
                    ->maxLength(255),
                TextInput::make('iban')
                    ->label('رقم الآيبان')
                    ->maxLength(34)
                    ->placeholder('SA0380000000608010167519')
                    ->helperText('اختياري - بين 15 و 34 خانة'),
                Select::make('account_id')
                    ->label('حساب الأستاذ')
                    ->relationship('account', 'name')
                    ->getOptionLabelFromRecordUsing(fn (Account $a) => "{$a->code} — {$a->name}")
                    ->required()
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
