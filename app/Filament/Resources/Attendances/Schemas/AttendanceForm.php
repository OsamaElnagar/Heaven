<?php

namespace App\Filament\Resources\Attendances\Schemas;

use App\Enums\AttendanceStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AttendanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('تسجيل الحضور والانصراف')
                    ->schema([
                        Select::make('employee_id')
                            ->label('الموظف')
                            ->relationship('employee', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        DatePicker::make('date')
                            ->label('التاريخ')
                            ->required()
                            ->default(now()),
                        TimePicker::make('check_in')
                            ->label('وقت الحضور'),
                        TimePicker::make('check_out')
                            ->label('وقت الانصراف'),
                        Select::make('status')
                            ->label('الحالة')
                            ->options(AttendanceStatus::class)
                            ->required()
                            ->default('present'),
                        TextInput::make('overtime_hours')
                            ->label('ساعات إضافية')
                            ->numeric()
                            ->default(0),
                        Textarea::make('notes')
                            ->label('ملاحظات'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
