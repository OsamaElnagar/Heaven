<?php

namespace App\Filament\Resources\Visas\Schemas;

use App\Enums\VisaStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VisaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات التأشيرة')
                    ->components([
                        Select::make('booking_id')
                            ->label('الحجز')
                            ->relationship('booking', 'reference')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false),
                        Select::make('status')
                            ->label('الحالة')
                            ->options(VisaStatus::class)
                            ->required()
                            ->native(false),
                        TextInput::make('visa_number')
                            ->label('رقم التأشيرة'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('التواريخ')
                    ->components([
                        DatePicker::make('applied_at')
                            ->label('تاريخ التقديم')
                            ->native(false),
                        DatePicker::make('approved_at')
                            ->label('تاريخ الموافقة')
                            ->native(false),
                        DatePicker::make('expiry_date')
                            ->label('تاريخ الانتهاء')
                            ->native(false),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('ملاحظات')
                    ->components([
                        Textarea::make('rejection_reason')
                            ->label('سبب الرفض')
                            ->columnSpanFull(),
                        Textarea::make('notes')
                            ->label('ملاحظات')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
