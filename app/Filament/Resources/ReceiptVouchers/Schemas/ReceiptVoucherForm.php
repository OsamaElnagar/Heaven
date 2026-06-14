<?php

namespace App\Filament\Resources\ReceiptVouchers\Schemas;

use App\Enums\PayerType;
use App\Enums\PaymentType;
use App\Enums\VoucherPaymentMethod;
use App\Filament\Resources\Bookings\BookingResource;
use App\Filament\Resources\Clients\ClientResource;
use App\Filament\Resources\Employees\EmployeeResource;
use App\Filament\Resources\Suppliers\SupplierResource;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ReceiptVoucherForm
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
                        Select::make('receipt_method')
                            ->label('طريقة التحصيل')
                            ->options(VoucherPaymentMethod::class)
                            ->required()
                            ->default('safe')
                            ->live()
                            ->helperText(fn (Get $get): ?string => match ($get('receipt_method')) {
                                'safe' => 'التحصيل نقداً في الخزينة',
                                'bank' => 'إيداع بنكي مباشر',
                                'cheque' => 'استلام شيك',
                                default => null,
                            }),
                        Select::make('safe_id')
                            ->label('الخزينة')
                            ->relationship('safe', 'name')
                            ->searchable()
                            ->preload()
                            ->required(fn (Get $get): bool => $get('receipt_method') === VoucherPaymentMethod::SAFE)
                            ->visible(fn (Get $get): bool => $get('receipt_method') === VoucherPaymentMethod::SAFE),
                        Select::make('bank_account_id')
                            ->label('الحساب البنكي')
                            ->relationship('bankAccount', 'bank_name')
                            ->searchable()
                            ->preload()
                            ->required(fn (Get $get): bool => in_array($get('receipt_method'), [VoucherPaymentMethod::BANK, VoucherPaymentMethod::CHEQUE], true))
                            ->visible(fn (Get $get): bool => in_array($get('receipt_method'), [VoucherPaymentMethod::BANK, VoucherPaymentMethod::CHEQUE], true)),
                        TextInput::make('cheque_number')
                            ->label('رقم الشيك')
                            ->visible(fn (Get $get): bool => $get('receipt_method') === VoucherPaymentMethod::CHEQUE),
                        DatePicker::make('cheque_date')
                            ->label('تاريخ الشيك')
                            ->visible(fn (Get $get): bool => $get('receipt_method') === VoucherPaymentMethod::CHEQUE),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('الدافع')
                    ->schema([
                        Select::make('payer_type')
                            ->label('نوع الدافع')
                            ->options(PayerType::class)
                            ->required()
                            ->live(),
                        Select::make('client_id')
                            ->label('العميل')
                            ->relationship('client', 'name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->visible(fn (Get $get): bool => $get('payer_type') === PayerType::CLIENT)
                            ->hintActions([
                                Action::make('viewClient')
                                    ->label('عرض العميل')
                                    ->visible(fn (Get $get): bool => (bool) $get('client_id'))
                                    ->icon(Heroicon::ArrowTopRightOnSquare)
                                    ->url(fn (Get $get) => ClientResource::getUrl('edit', ['record' => $get('client_id')]))
                                    ->openUrlInNewTab(),
                            ]),
                        Select::make('booking_id')
                            ->label('الحجز')
                            ->relationship('booking', 'reference', modifyQueryUsing: fn ($query, Get $get) => $query->where('client_id', $get('client_id')))
                            ->searchable()
                            ->preload()
                            ->visible(fn (Get $get): bool => $get('payer_type') === PayerType::CLIENT)
                            ->hintActions([
                                Action::make('viewBooking')
                                    ->label('عرض الحجز')
                                    ->visible(fn (Get $get): bool => (bool) $get('booking_id'))
                                    ->icon(Heroicon::ArrowTopRightOnSquare)
                                    ->url(fn (Get $get) => BookingResource::getUrl('edit', ['record' => $get('booking_id')]))
                                    ->openUrlInNewTab(),
                            ]),
                        Select::make('payment_type')
                            ->label('نوع الدفعة')
                            ->options(PaymentType::class)
                            ->visible(fn (Get $get): bool => $get('payer_type') === PayerType::CLIENT),
                        Select::make('supplier_id')
                            ->label('المورد')
                            ->relationship('supplier', 'name', modifyQueryUsing: fn ($query) => $query->where('is_active', true))
                            ->searchable()
                            ->preload()
                            ->visible(fn (Get $get): bool => $get('payer_type') === PayerType::SUPPLIER)
                            ->hintActions([
                                Action::make('viewSupplier')
                                    ->label('عرض المورد')
                                    ->visible(fn (Get $get): bool => (bool) $get('supplier_id'))
                                    ->icon(Heroicon::ArrowTopRightOnSquare)
                                    ->url(fn (Get $get) => SupplierResource::getUrl('edit', ['record' => $get('supplier_id')]))
                                    ->openUrlInNewTab(),
                            ]),
                        Select::make('employee_id')
                            ->label('الموظف')
                            ->relationship('employee', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn (Get $get): bool => $get('payer_type') === PayerType::EMPLOYEE)
                            ->hintActions([
                                Action::make('viewEmployee')
                                    ->label('عرض الموظف')
                                    ->visible(fn (Get $get): bool => (bool) $get('employee_id'))
                                    ->icon(Heroicon::ArrowTopRightOnSquare)
                                    ->url(fn (Get $get) => EmployeeResource::getUrl('edit', ['record' => $get('employee_id')]))
                                    ->openUrlInNewTab(),
                            ]),
                        TextInput::make('payer_name')
                            ->label('اسم الدافع')
                            ->visible(fn (Get $get): bool => $get('payer_type') === PayerType::OTHER),
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
                    ])
                    ->columns(2)
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
                            ->directory('receipt-vouchers')
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
}
