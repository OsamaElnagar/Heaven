<?php

namespace App\Filament\Resources\Accounts\Schemas;

use App\Enums\AccountClass;
use App\Enums\AccountNormalBalance;
use App\Enums\AccountType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('الرقم')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('name')
                    ->label('الاسم')
                    ->required()
                    ->maxLength(255),
                TextInput::make('name_en')
                    ->label('الاسم بالإنجليزية')
                    ->maxLength(255),
                Select::make('class')
                    ->label('الفئة')
                    ->options(AccountClass::class)
                    ->required(),
                Select::make('type')
                    ->label('النوع')
                    ->options(AccountType::class)
                    ->required()
                    ->default('detail'),
                Select::make('normal_balance')
                    ->label('الرصيد الطبيعي')
                    ->options(AccountNormalBalance::class)
                    ->required(),
                Select::make('parent_id')
                    ->label('الحساب الأب')
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('level')
                    ->label('المستوى')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->maxValue(10),
                Toggle::make('is_active')
                    ->label('نشط')
                    ->default(true)
                    ->required(),
                Toggle::make('is_system')
                    ->label('نظامي')
                    ->default(false)
                    ->required(),
                Textarea::make('notes')
                    ->label('ملاحظات')
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }
}
