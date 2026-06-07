<?php

namespace App\Filament\Resources\Bookings\Actions;

use App\Enums\ExpenseStatus;
use App\Enums\PayerType;
use App\Enums\PaymentType;
use App\Enums\VoucherPaymentMethod;
use App\Models\BankAccount;
use App\Models\Booking;
use App\Models\ReceiptVoucher;
use App\Models\Safe;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class RecordPaymentAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'recordPayment';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('تسجيل دفعة')
            ->icon('heroicon-o-currency-dollar')
            ->color('success')
            ->schema([
                TextInput::make('amount')
                    ->label('المبلغ')
                    ->required()
                    ->numeric()
                    ->prefix('ج.م')
                    ->minValue(1),
                Select::make('payment_type')
                    ->label('النوع')
                    ->options(PaymentType::class)
                    ->required()
                    ->default(PaymentType::INSTALLMENT->value)
                    ->native(false),
                Select::make('receipt_method')
                    ->label('طريقة الدفع')
                    ->options(VoucherPaymentMethod::class)
                    ->required()
                    ->default(VoucherPaymentMethod::SAFE->value)
                    ->live()
                    ->native(false),
                Select::make('safe_id')
                    ->label('الخزينة')
                    ->options(fn () => Safe::where('is_active', true)->pluck('name', 'id'))
                    ->visible(fn ($get) => $get('receipt_method') === VoucherPaymentMethod::SAFE->value)
                    ->required(fn ($get) => $get('receipt_method') === VoucherPaymentMethod::SAFE->value)
                    ->native(false),
                Select::make('bank_account_id')
                    ->label('الحساب البنكي')
                    ->options(fn () => BankAccount::where('is_active', true)
                        ->get()
                        ->mapWithKeys(fn ($b) => [$b->id => $b->bank_name.' - '.$b->account_number])
                        ->toArray())
                    ->visible(fn ($get) => $get('receipt_method') !== VoucherPaymentMethod::SAFE->value)
                    ->required(fn ($get) => $get('receipt_method') !== VoucherPaymentMethod::SAFE->value)
                    ->native(false),
                TextInput::make('reference')
                    ->label('مرجع / رقم شيك')
                    ->maxLength(255),
                DatePicker::make('voucher_date')
                    ->label('تاريخ الدفع')
                    ->required()
                    ->default(now())
                    ->native(false),
                Textarea::make('description')
                    ->label('ملاحظات')
                    ->rows(2),
                Hidden::make('booking_id'),
            ])
            ->modalHeading('تسجيل دفعة')
            ->slideOver()
            ->action(function (Booking $record, array $data) {
                $rv = ReceiptVoucher::create([
                    'voucher_date' => $data['voucher_date'],
                    'receipt_method' => $data['receipt_method'],
                    'safe_id' => $data['receipt_method'] === VoucherPaymentMethod::SAFE->value ? $data['safe_id'] : null,
                    'bank_account_id' => $data['receipt_method'] !== VoucherPaymentMethod::SAFE->value ? $data['bank_account_id'] : null,
                    'amount' => (int) $data['amount'],
                    'payment_type' => $data['payment_type'],
                    'payer_type' => PayerType::CLIENT,
                    'client_id' => $record->client_id,
                    'booking_id' => $record->id,
                    'description' => $data['description'] ?? 'دفعة على الحجز #'.$record->id,
                    'reference' => $data['reference'] ?? null,
                    'status' => ExpenseStatus::POSTED,
                    'created_by' => Auth::id() ?? 1,
                ]);

                if ($rv->journal_entry_id) {
                    Notification::make()
                        ->title('تم تسجيل الدفعة وترحيل القيد')
                        ->body('رقم السند: '.$rv->number)
                        ->success()
                        ->send();
                } else {
                    Notification::make()
                        ->title('تم تسجيل الدفعة كمسودة')
                        ->body('لم يتم ترحيل القيد - تأكد من وجود خزينة/بنك نشط')
                        ->warning()
                        ->send();
                }
            });
    }
}
