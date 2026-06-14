<?php

namespace App\Filament\Resources\EmployeeAdvances\Schemas;

use App\Enums\EmployeeAdvanceStatus;
use App\Enums\EmployeeAdvanceType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EmployeeAdvanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('معلومات سلفة الموظف')
                    ->components([
                        TextInput::make('code')
                            ->label('رقم المستند')
                            ->hiddenOn('create')
                            ->readOnly()
                            ->dehydrated(false),
                        Select::make('employee_id')
                            ->label('الموظف')
                            ->relationship('employee', 'name')
                            ->required(),
                        DatePicker::make('advance_date')
                            ->label('تاريخ السلفة')
                            ->required(),
                        TextInput::make('amount')
                            ->label('المبلغ')
                            ->numeric()
                            ->required(),
                        TextInput::make('repaid_amount')
                            ->label('المبلغ المُسدّد')
                            ->numeric(),
                        TextInput::make('installments')
                            ->label('عدد الأقساط')
                            ->numeric(),
                        Select::make('type')
                            ->label('النوع')
                            ->options(EmployeeAdvanceType::class)
                            ->required()
                            ->default('short_term'),
                        Select::make('status')
                            ->label('الحالة')
                            ->options(EmployeeAdvanceStatus::class)
                            ->required(),
                        Select::make('safe_id')
                            ->label('الخزنة')
                            ->relationship('safe', 'name'),
                        Select::make('journal_entry_id')
                            ->label('قيد اليومية')
                            ->relationship('journalEntry', 'code')
                            ->hiddenOn('create'),
                        Textarea::make('notes')
                            ->label('ملاحظات'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
