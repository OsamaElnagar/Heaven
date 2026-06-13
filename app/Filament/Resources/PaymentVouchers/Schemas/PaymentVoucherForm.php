<?php

namespace App\Filament\Resources\PaymentVouchers\Schemas;

use App\Enums\PayeeType;
use App\Enums\VoucherPaymentMethod;
use App\Filament\Resources\Agents\AgentResource;
use App\Filament\Resources\Branches\BranchResource;
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
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

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
                            ->live()
                            ->helperText(fn (Get $get): ?string => match ($get('payment_method')) {
                                'safe' => 'الصرف نقداً من الخزينة',
                                'bank' => 'تحويل بنكي مباشر',
                                'cheque' => 'إصدار شيك',
                                default => null,
                            }),
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
                            ->relationship('supplier', 'name', modifyQueryUsing: fn ($query) => $query->where('is_active', true))
                            ->searchable()
                            ->preload()
                            ->visible(fn (Get $get): bool => $get('payee_type') === PayeeType::SUPPLIER)
                            ->hintActions([
                                Action::make('viewSupplier')
                                    ->label('عرض المورد')
                                    ->visible(fn (Get $get): bool => (bool) $get('supplier_id'))
                                    ->icon(Heroicon::ArrowTopRightOnSquare)
                                    ->url(fn (Get $get) => SupplierResource::getUrl('edit', ['record' => $get('supplier_id')]))
                                    ->openUrlInNewTab(),
                            ]),
                        Select::make('client_id')
                            ->label('العميل')
                            ->relationship('client', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn (Get $get): bool => $get('payee_type') === PayeeType::CLIENT)
                            ->hintActions([
                                Action::make('viewClient')
                                    ->label('عرض العميل')
                                    ->visible(fn (Get $get): bool => (bool) $get('client_id'))
                                    ->icon(Heroicon::ArrowTopRightOnSquare)
                                    ->url(fn (Get $get) => ClientResource::getUrl('edit', ['record' => $get('client_id')]))
                                    ->openUrlInNewTab(),
                            ]),
                        Select::make('employee_id')
                            ->label('الموظف')
                            ->relationship('employee', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn (Get $get): bool => $get('payee_type') === PayeeType::EMPLOYEE)
                            ->hintActions([
                                Action::make('viewEmployee')
                                    ->label('عرض الموظف')
                                    ->visible(fn (Get $get): bool => (bool) $get('employee_id'))
                                    ->icon(Heroicon::ArrowTopRightOnSquare)
                                    ->url(fn (Get $get) => EmployeeResource::getUrl('edit', ['record' => $get('employee_id')]))
                                    ->openUrlInNewTab(),
                            ]),
                        Select::make('branch_id')
                            ->label('الفرع')
                            ->relationship('branch', 'name', modifyQueryUsing: fn ($query) => $query->where('is_active', true))
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->visible(fn (Get $get): bool => $get('payee_type') === PayeeType::BRANCH)
                            ->hintActions([
                                Action::make('viewBranch')
                                    ->label('عرض الفرع')
                                    ->visible(fn (Get $get): bool => (bool) $get('branch_id'))
                                    ->icon(Heroicon::ArrowTopRightOnSquare)
                                    ->url(fn (Get $get) => BranchResource::getUrl('edit', ['record' => $get('branch_id')]))
                                    ->openUrlInNewTab(),
                            ]),
                        Select::make('agent_id')
                            ->label('الوكيل')
                            ->relationship('agent', 'name', modifyQueryUsing: fn ($query) => $query->where('is_active', true))
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->visible(fn (Get $get): bool => $get('payee_type') === PayeeType::AGENT)
                            ->hintActions([
                                Action::make('viewAgent')
                                    ->label('عرض الوكيل')
                                    ->visible(fn (Get $get): bool => (bool) $get('agent_id'))
                                    ->icon(Heroicon::ArrowTopRightOnSquare)
                                    ->url(fn (Get $get) => AgentResource::getUrl('edit', ['record' => $get('agent_id')]))
                                    ->openUrlInNewTab(),
                            ]),
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
