<?php

namespace App\Filament\Resources\Bookings\Schemas;

use App\Enums\BookingStatus;
use App\Enums\RoomType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات الحجز')
                    ->components([
                        Select::make('client_id')
                            ->label('العميل')
                            ->relationship('client', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false),
                        Select::make('package_id')
                            ->label('الباقة')
                            ->relationship('package', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false),
                        Select::make('trip_id')
                            ->label('الرحلة')
                            ->relationship('trip', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false),
                        Select::make('status')
                            ->label('الحالة')
                            ->options(BookingStatus::class)
                            ->required()
                            ->default(BookingStatus::PENDING)
                            ->native(false),
                        Select::make('room_type')
                            ->label('نوع الغرفة')
                            ->options(RoomType::class)
                            ->required()
                            ->native(false),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('التسعير')
                    ->components([
                        TextInput::make('total_price')
                            ->label('السعر الإجمالي')
                            ->required()
                            ->numeric()
                            ->prefix('ج.م'),
                        TextInput::make('discount')
                            ->label('الخصم')
                            ->numeric()
                            ->default(0)
                            ->prefix('ج.م'),
                        TextInput::make('net_price')
                            ->label('صافي السعر')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('paid_amount')
                            ->label('المبلغ المدفوع')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('معلومات إضافية')
                    ->components([
                        Select::make('room_id')
                            ->label('الغرفة')
                            ->relationship('room', 'room_number')
                            ->searchable()
                            ->preload()
                            ->native(false),
                        DatePicker::make('due_date')
                            ->label('تاريخ الاستحقاق')
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
