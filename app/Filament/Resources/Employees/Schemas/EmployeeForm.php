<?php

namespace App\Filament\Resources\Employees\Schemas;

use App\Enums\EmployeeType;
use App\Enums\SalaryType;
use App\Models\Account;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('معلومات الموظف')
                    ->schema([
                        TextInput::make('code')
                            ->label('كود الموظف')
                            ->readOnly()
                            ->dehydrated(false),
                        TextInput::make('name')
                            ->label('اسم الموظف')
                            ->required(),
                        TextInput::make('job_title')
                            ->label('المسمى الوظيفي'),
                        TextInput::make('national_id')
                            ->label('الرقم القومي')
                            ->required(),
                        TextInput::make('phone')
                            ->label('رقم الهاتف')
                            ->tel()
                            ->required(),
                        TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email(),
                        TextInput::make('address')
                            ->label('العنوان'),
                        Select::make('department_id')
                            ->relationship('department', 'name', modifyQueryUsing: fn ($query) => $query->where('is_active', true))
                            ->label('القسم')
                            ->searchable()
                            ->preload(),
                        Select::make('type')
                            ->options(EmployeeType::class)
                            ->required()
                            ->label('النوع'),
                        Select::make('role')
                            ->label('الدور')
                            ->options([
                                'sales' => 'مندوب مبيعات',
                                'operations' => 'مسؤول عمليات',
                                'accountant' => 'محاسب',
                                'guide' => 'مرشد ديني',
                                'manager' => 'مدير',
                            ])
                            ->required(),
                        Select::make('salary_type')
                            ->options(SalaryType::class)
                            ->required()
                            ->label('نوع المرتب'),
                        TextInput::make('daily_hours')
                            ->label('ساعات العمل اليومية')
                            ->numeric()
                            ->suffix('ساعة')
                            ->default(8),
                        TextInput::make('base_salary')
                            ->label('الراتب الأساسي')
                            ->suffix('EGP')
                            ->integer(),
                        DatePicker::make('hire_date')
                            ->label('تاريخ التعيين')
                            ->required()
                            ->default(now()),
                        DatePicker::make('termination_date')
                            ->label('تاريخ الإنهاء')
                            ->maxDate(now()),
                        Toggle::make('is_active')
                            ->label('نشط')
                            ->default(true),
                        Select::make('account_id')
                            ->label('الحساب')
                            ->relationship('account', 'name')
                            ->getOptionLabelFromRecordUsing(fn (Account $a) => "{$a->code} — {$a->name}")
                            ->searchable()
                            ->preload(),
                        Select::make('advance_account_id')
                            ->label('حساب السلف')
                            ->relationship('advanceAccount', 'name')
                            ->getOptionLabelFromRecordUsing(fn (Account $a) => "{$a->code} — {$a->name}")
                            ->searchable()
                            ->preload(),
                        Select::make('user_id')
                            ->label('حساب المستخدم')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload(),
                        RichEditor::make('notes')
                            ->label('ملاحظات'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
