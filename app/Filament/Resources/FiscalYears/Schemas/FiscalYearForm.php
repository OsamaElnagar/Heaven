<?php

namespace App\Filament\Resources\FiscalYears\Schemas;

use App\Enums\FiscalYearStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;

class FiscalYearForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات السنة المالية')
                    ->components([
                        TextInput::make('name')
                            ->label('الاسم')
                            ->required()
                            ->maxLength(255)
                            ->default(Carbon::now()->year),
                        DatePicker::make('starts_at')
                            ->label('تاريخ البدء')
                            ->required()
                            ->default(Carbon::now()->startOfYear()),
                        DatePicker::make('ends_at')
                            ->label('تاريخ الانتهاء')
                            ->required()
                            ->default(Carbon::now()->endOfYear()),
                        Select::make('status')
                            ->label('الحالة')
                            ->options(FiscalYearStatus::class)
                            ->required()
                            ->default(FiscalYearStatus::OPEN),
                        DateTimePicker::make('closed_at')
                            ->label('تاريخ الإغلاق')
                            ->disabled()
                            ->dehydrated(false),
                        Hidden::make('closed_by'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
