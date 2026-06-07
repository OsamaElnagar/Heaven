<?php

namespace App\Filament\Resources\RefundVouchers\Schemas;

use App\Enums\ExpenseStatus;
use App\Enums\RefundPartyType;
use App\Enums\VoucherPaymentMethod;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class RefundVoucherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات السند')
                    ->schema([
                        TextInput::make('number')
                            ->label('رقم السند')
                            ->hiddenOn('create')
                            ->readOnly()
                            ->dehydrated(false),
                        DatePicker::make('voucher_date')
                            ->label('تاريخ السند')
                            ->default(now())
                            ->required(),
                        Select::make('payment_method')
                            ->label('طريقة الدفع')
                            ->options(VoucherPaymentMethod::class)
                            ->required()
                            ->default('safe')
                            ->live(),
                        Select::make('safe_id')
                            ->label('الخزينة')
                            ->relationship('safe', 'name')
                            ->searchable()
                            ->preload()
                            ->required(fn (Get $get): bool => $get('payment_method') === VoucherPaymentMethod::SAFE)
                            ->visible(fn (Get $get): bool => $get('payment_method') === VoucherPaymentMethod::SAFE),
                        Select::make('bank_account_id')
                            ->label('الحساب البنكي')
                            ->relationship('bankAccount', 'bank_name')
                            ->searchable()
                            ->preload()
                            ->required(fn (Get $get): bool => in_array($get('payment_method'), [VoucherPaymentMethod::BANK, VoucherPaymentMethod::CHEQUE], true))
                            ->visible(fn (Get $get): bool => in_array($get('payment_method'), [VoucherPaymentMethod::BANK, VoucherPaymentMethod::CHEQUE], true)),
                        TextInput::make('cheque_number')
                            ->label('رقم الشيك')
                            ->visible(fn (Get $get): bool => $get('payment_method') === VoucherPaymentMethod::CHEQUE),
                        DatePicker::make('cheque_date')
                            ->label('تاريخ الشيك')
                            ->visible(fn (Get $get): bool => $get('payment_method') === VoucherPaymentMethod::CHEQUE),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('الطرف')
                    ->schema([
                        Select::make('party_type')
                            ->label('نوع الطرف')
                            ->options(RefundPartyType::class)
                            ->required()
                            ->live(),
                        Select::make('client_id')
                            ->label('العميل')
                            ->relationship('client', 'name')
                            ->searchable()
                            ->preload()
                            ->required(fn (Get $get): bool => $get('party_type') === RefundPartyType::CLIENT)
                            ->visible(fn (Get $get): bool => $get('party_type') === RefundPartyType::CLIENT)
                            ->live(),
                        Select::make('supplier_id')
                            ->label('المورد')
                            ->relationship('supplier', 'name')
                            ->searchable()
                            ->preload()
                            ->required(fn (Get $get): bool => $get('party_type') === RefundPartyType::SUPPLIER)
                            ->visible(fn (Get $get): bool => $get('party_type') === RefundPartyType::SUPPLIER),
                        Select::make('booking_id')
                            ->label('الحجز')
                            ->relationship('booking', 'reference')
                            ->searchable()
                            ->preload()
                            ->visible(fn (Get $get): bool => $get('party_type') === RefundPartyType::CLIENT),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('المبلغ')
                    ->schema([
                        TextInput::make('amount')
                            ->label('المبلغ')
                            ->required()
                            ->numeric()
                            ->minValue(1),
                        TextInput::make('reference')
                            ->label('مرجع نصي (اختياري)'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('إضافات')
                    ->schema([
                        Textarea::make('description')
                            ->label('البيان')
                            ->columnSpanFull(),
                        FileUpload::make('attachment')
                            ->label('المرفق')
                            ->directory('refund-vouchers')
                            ->visibility('public'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Hidden::make('status')
                    ->default(ExpenseStatus::DRAFT),
                Hidden::make('created_by')
                    ->default(fn () => auth('web')->id()),
            ]);
    }
}
