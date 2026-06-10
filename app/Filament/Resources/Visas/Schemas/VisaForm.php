<?php

namespace App\Filament\Resources\Visas\Schemas;

use App\Enums\VisaStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
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
                            ->live()
                            ->native(false),
                        TextInput::make('visa_number')
                            ->label('رقم التأشيرة')
                            ->visible(fn (Get $get): bool => $get('status') === VisaStatus::APPROVED->value),
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
                            ->native(false)
                            ->visible(fn (Get $get): bool => $get('status') === VisaStatus::APPROVED->value),
                        DatePicker::make('expiry_date')
                            ->label('تاريخ الانتهاء')
                            ->native(false)
                            ->visible(fn (Get $get): bool => $get('status') === VisaStatus::APPROVED->value),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('ملاحظات')
                    ->components([
                        Textarea::make('rejection_reason')
                            ->label('سبب الرفض')
                            ->columnSpanFull()
                            ->visible(fn (Get $get): bool => $get('status') === VisaStatus::REJECTED->value),
                        Textarea::make('notes')
                            ->label('ملاحظات')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
