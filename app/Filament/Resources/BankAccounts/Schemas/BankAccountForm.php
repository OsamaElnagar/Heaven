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
                TextInput::make('code')
                    ->label('الرقم')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->readOnly()
                    ->dehydrated(true),
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
                    ->helperText('اختياري - بين 15 و 34 خانة')
                    ->regex('/^[A-Z]{2}\d{2}[A-Z0-9]{11,30}$/')
                    ->rules('nullable'),
                Select::make('account_id')
                    ->label('حساب الأستاذ')
                    ->relationship('account', 'name')
                    ->getOptionLabelFromRecordUsing(fn (Account $a) => "{$a->code} — {$a->name}")
                    ->searchable()
                    ->preload()
                    ->options(fn () => Account::where('type', 'detail')
                        ->where('is_active', true)
                        ->where('code', 'like', '1233%')
                        ->pluck('name', 'id'))
                    ->helperText('يتم إنشاء الحساب تلقائياً إذا لم يتم تحديده'),
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
