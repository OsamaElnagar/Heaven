<?php

namespace App\Filament\Resources\Employees\Schemas;

use App\Enums\SalaryType;
use Filament\Forms\Components\DatePicker;
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
                Section::make('البيانات الشخصية')
                    ->components([
                        TextInput::make('name')
                            ->label('الاسم')
                            ->required(),
                        TextInput::make('national_id')
                            ->label('الرقم القومي')
                            ->required(),
                        TextInput::make('phone')
                            ->label('رقم الهاتف')
                            ->tel()
                            ->required(),
                        TextInput::make('role')
                            ->label('المسمى الوظيفي')
                            ->required(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('الراتب والتوظيف')
                    ->components([
                        Select::make('salary_type')
                            ->label('نوع الراتب')
                            ->options(SalaryType::class)
                            ->required()
                            ->native(false),
                        TextInput::make('salary')
                            ->label('الراتب')
                            ->required()
                            ->numeric()
                            ->prefix('ج.م'),
                        DatePicker::make('hired_at')
                            ->label('تاريخ التعيين')
                            ->required()
                            ->default(now())
                            ->native(false),
                        DatePicker::make('left_at')
                            ->label('تاريخ ترك العمل')
                            ->native(false),
                        Toggle::make('is_active')
                            ->label('نشط')
                            ->default(true),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('حساب المستخدم')
                    ->components([
                        Select::make('user_id')
                            ->label('حساب المستخدم')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
