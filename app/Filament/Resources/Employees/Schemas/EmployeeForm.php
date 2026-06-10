<?php

namespace App\Filament\Resources\Employees\Schemas;

use App\Enums\SalaryType;
use App\Models\Employee;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
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
                            ->required()
                            ->unique(Employee::class, 'national_id', ignoreRecord: true),
                        TextInput::make('phone')
                            ->label('رقم الهاتف')
                            ->tel()
                            ->required(),
                        Select::make('role')
                            ->label('المسمى الوظيفي')
                            ->options(fn () => Employee::query()
                                ->distinct()
                                ->whereNotNull('role')
                                ->pluck('role', 'role')
                            )
                            ->creatable()
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
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                static::updateMonthlyEquivalent($set, $get);
                            }),
                        TextInput::make('salary')
                            ->label('الراتب')
                            ->required()
                            ->numeric()
                            ->prefix('ج.م')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                static::updateMonthlyEquivalent($set, $get);
                            }),
                        TextInput::make('monthly_equivalent')
                            ->label('المكافئ الشهري')
                            ->numeric()
                            ->prefix('ج.م')
                            ->disabled()
                            ->dehydrated(false),
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

    protected static function updateMonthlyEquivalent(Set $set, Get $get): void
    {
        $salaryType = $get('salary_type');
        $salary = (float) ($get('salary') ?? 0);

        if (! $salaryType || ! $salary) {
            return;
        }

        $monthly = match (SalaryType::tryFrom($salaryType)) {
            SalaryType::MONTHLY => $salary,
            SalaryType::DAILY => $salary * 30,
            SalaryType::PER_TRIP => $salary,
            SalaryType::COMMISSION => $salary,
            default => $salary,
        };

        $set('monthly_equivalent', $monthly);
    }
}
