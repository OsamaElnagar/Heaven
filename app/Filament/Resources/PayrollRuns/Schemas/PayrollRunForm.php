<?php

namespace App\Filament\Resources\PayrollRuns\Schemas;

use App\Enums\PayrollRunStatus;
use App\Enums\PayrollRunType;
use App\Models\FiscalYear;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PayrollRunForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات أساسية')
                    ->schema([
                        TextInput::make('code')
                            ->label('الرقم')
                            ->hiddenOn('create')
                            ->readOnly()
                            ->dehydrated(false),
                        Select::make('fiscal_year_id')
                            ->label('السنة المالية')
                            ->relationship('fiscalYear', 'name')
                            ->default(fn () => FiscalYear::where('status', 'open')->first()?->id)
                            ->required(),
                        Select::make('month')
                            ->label('الشهر')
                            ->options([
                                1 => 'يناير',
                                2 => 'فبراير',
                                3 => 'مارس',
                                4 => 'أبريل',
                                5 => 'مايو',
                                6 => 'يونيو',
                                7 => 'يوليو',
                                8 => 'أغسطس',
                                9 => 'سبتمبر',
                                10 => 'أكتوبر',
                                11 => 'نوفمبر',
                                12 => 'ديسمبر',
                            ])
                            ->required(),
                        TextInput::make('year')
                            ->label('السنة')
                            ->numeric()
                            ->default(now()->year)
                            ->required(),
                        Select::make('type')
                            ->label('النوع')
                            ->options(PayrollRunType::class)
                            ->default('monthly')
                            ->required(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('الإجماليات')
                    ->schema([
                        TextInput::make('total_gross')
                            ->label('إجمالي المستحقات')
                            ->numeric()
                            ->suffix('ج.م')
                            ->default(0)
                            ->readOnly()
                            ->dehydrated(true),
                        TextInput::make('total_deductions')
                            ->label('إجمالي الخصومات')
                            ->numeric()
                            ->suffix('ج.م')
                            ->default(0)
                            ->readOnly()
                            ->dehydrated(true),
                        TextInput::make('total_net')
                            ->label('صافي المستحقات')
                            ->numeric()
                            ->suffix('ج.م')
                            ->default(0)
                            ->readOnly()
                            ->dehydrated(true),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                Section::make('بيانات النظام')
                    ->schema([
                        Select::make('status')
                            ->label('الحالة')
                            ->options(PayrollRunStatus::class)
                            ->default('draft')
                            ->hiddenOn('create'),
                        Select::make('journal_entry_id')
                            ->label('قيد اليومية')
                            ->relationship('journalEntry', 'code')
                            ->hiddenOn('create'),
                    ])
                    ->hiddenOn('create')
                    ->columns(2)
                    ->columnSpanFull(),

                Hidden::make('created_by')
                    ->default(fn () => auth('web')->id()),
            ]);
    }
}
