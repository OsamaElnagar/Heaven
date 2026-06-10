<?php

namespace App\Filament\Resources\PaymentVouchers\Schemas;

use App\Enums\PayeeType;
use App\Enums\VoucherPaymentMethod;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class PaymentVoucherForm
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
                Section::make('المستلم')
                    ->schema([
                        Select::make('payee_type')
                            ->label('نوع المستلم')
                            ->options(PayeeType::class)
                            ->required()
                            ->live(),
                        Select::make('supplier_id')
                            ->label('المورد')
                            ->relationship('supplier', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn (Get $get): bool => $get('payee_type') === PayeeType::SUPPLIER),
                        Select::make('client_id')
                            ->label('العميل')
                            ->relationship('client', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn (Get $get): bool => $get('payee_type') === PayeeType::CLIENT),
                        Select::make('employee_id')
                            ->label('الموظف')
                            ->relationship('employee', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn (Get $get): bool => $get('payee_type') === PayeeType::EMPLOYEE),
                        TextInput::make('payee_name')
                            ->label('اسم المستلم')
                            ->visible(fn (Get $get): bool => $get('payee_type') === PayeeType::OTHER),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('المبالغ')
                    ->schema([
                        TextInput::make('amount')
                            ->label('المبلغ')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Get $get, Set $set) => self::updateNetAmount($get, $set)),
                        TextInput::make('withholding_amount')
                            ->label('ضريبة مقتطعة')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Get $get, Set $set) => self::updateNetAmount($get, $set)),
                        TextInput::make('net_amount')
                            ->label('الصافي')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
                Section::make('إضافات')
                    ->schema([
                        Textarea::make('description')
                            ->label('البيان')
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('reference')
                            ->label('المرجع'),
                        FileUpload::make('attachment')
                            ->label('المرفق')
                            ->directory('payment-vouchers')
                            ->visibility('public'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Hidden::make('status')
                    ->default('draft'),
                Hidden::make('created_by')
                    ->default(fn () => auth('web')->id()),
            ]);
    }

    public static function updateNetAmount(Get $get, Set $set): void
    {
        $amount = (float) ($get('amount') ?? 0);
        $withholding = (float) ($get('withholding_amount') ?? 0);
        $set('net_amount', $amount - $withholding);
    }
}
