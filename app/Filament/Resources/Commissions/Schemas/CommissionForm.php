<?php

namespace App\Filament\Resources\Commissions\Schemas;

use App\Enums\CommissionStatus;
use App\Enums\CommissionType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CommissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات العمولة')
                    ->components([
                        Select::make('booking_id')
                            ->label('الحجز')
                            ->relationship('booking', 'reference')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false),
                        Select::make('branch_id')
                            ->label('الفرع')
                            ->relationship('branch', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false),
                        Select::make('agent_id')
                            ->label('الوكيل')
                            ->relationship('agent', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false),
                        Select::make('commission_type')
                            ->label('نوع العمولة')
                            ->options(CommissionType::class)
                            ->default('percentage')
                            ->required()
                            ->native(false),
                        TextInput::make('commission_rate')
                            ->label('قيمة العمولة')
                            ->numeric()
                            ->required(),
                        TextInput::make('amount')
                            ->label('المبلغ')
                            ->numeric()
                            ->prefix('ج.م')
                            ->required(),
                        Select::make('status')
                            ->label('الحالة')
                            ->options(CommissionStatus::class)
                            ->default('pending')
                            ->required()
                            ->native(false),
                        DateTimePicker::make('paid_at')
                            ->label('تاريخ الدفع')
                            ->native(false),
                        Select::make('payment_voucher_id')
                            ->label('سند الصرف')
                            ->relationship('paymentVoucher', 'number')
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
