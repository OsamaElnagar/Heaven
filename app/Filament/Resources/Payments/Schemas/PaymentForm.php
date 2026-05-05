<?php

namespace App\Filament\Resources\Payments\Schemas;

use App\Enums\PaymentMethod;
use App\Enums\PaymentType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات الدفعة')
                    ->components([
                        Select::make('booking_id')
                            ->label('الحجز')
                            ->relationship('booking', 'reference')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false),
                        Select::make('type')
                            ->label('النوع')
                            ->options(PaymentType::class)
                            ->required()
                            ->native(false),
                        Select::make('method')
                            ->label('طريقة الدفع')
                            ->options(PaymentMethod::class)
                            ->required()
                            ->native(false),
                        TextInput::make('amount')
                            ->label('المبلغ')
                            ->required()
                            ->numeric()
                            ->prefix('ج.م'),
                        DatePicker::make('paid_at')
                            ->label('تاريخ الدفع')
                            ->required()
                            ->default(now())
                            ->native(false),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('تفاصيل إضافية')
                    ->components([
                        TextInput::make('reference')
                            ->label('رقم مرجعي')
                            ->placeholder('رقم الشيك / الحوالة'),
                        TextInput::make('bank_name')
                            ->label('اسم البنك'),
                        Select::make('received_by')
                            ->label('استلم بواسطة')
                            ->relationship('receivedBy', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false),
                        Textarea::make('notes')
                            ->label('ملاحظات')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
