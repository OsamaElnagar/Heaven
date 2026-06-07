<?php

namespace App\Filament\Resources\Bookings\Actions;

use App\Enums\ExpenseStatus;
use App\Enums\RefundPartyType;
use App\Enums\VoucherPaymentMethod;
use App\Models\BankAccount;
use App\Models\Booking;
use App\Models\RefundVoucher;
use App\Models\Safe;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class IssueRefundAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'issueRefund';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('استرداد')
            ->icon('heroicon-o-arrow-uturn-left')
            ->color('danger')
            ->schema([
                TextInput::make('amount')
                    ->label('المبلغ')
                    ->required()
                    ->numeric()
                    ->prefix('ج.م')
                    ->minValue(1),
                Select::make('payment_method')
                    ->label('طريقة الدفع')
                    ->options(VoucherPaymentMethod::class)
                    ->required()
                    ->default(VoucherPaymentMethod::SAFE->value)
                    ->live()
                    ->native(false),
                Select::make('safe_id')
                    ->label('الخزينة')
                    ->options(fn () => Safe::where('is_active', true)->pluck('name', 'id'))
                    ->visible(fn ($get) => $get('payment_method') === VoucherPaymentMethod::SAFE->value)
                    ->required(fn ($get) => $get('payment_method') === VoucherPaymentMethod::SAFE->value)
                    ->native(false),
                Select::make('bank_account_id')
                    ->label('الحساب البنكي')
                    ->options(fn () => BankAccount::where('is_active', true)->pluck('name', 'id'))
                    ->visible(fn ($get) => $get('payment_method') !== VoucherPaymentMethod::SAFE->value)
                    ->required(fn ($get) => $get('payment_method') !== VoucherPaymentMethod::SAFE->value)
                    ->native(false),
                TextInput::make('reference')
                    ->label('مرجع')
                    ->maxLength(255),
                DatePicker::make('voucher_date')
                    ->label('تاريخ الاسترداد')
                    ->required()
                    ->default(now())
                    ->native(false),
                Textarea::make('description')
                    ->label('سبب الاسترداد')
                    ->rows(2)
                    ->required(),
            ])
            ->modalHeading('استرداد مبلغ')
            ->action(function (Booking $record, array $data) {
                $rf = RefundVoucher::create([
                    'voucher_date' => $data['voucher_date'],
                    'payment_method' => $data['payment_method'],
                    'safe_id' => $data['payment_method'] === VoucherPaymentMethod::SAFE->value ? $data['safe_id'] : null,
                    'bank_account_id' => $data['payment_method'] !== VoucherPaymentMethod::SAFE->value ? $data['bank_account_id'] : null,
                    'amount' => (int) $data['amount'],
                    'party_type' => RefundPartyType::CLIENT,
                    'client_id' => $record->client_id,
                    'booking_id' => $record->id,
                    'description' => $data['description'],
                    'reference' => $data['reference'] ?? null,
                    'status' => ExpenseStatus::POSTED,
                    'created_by' => Auth::id() ?? 1,
                ]);

                if ($rf->journal_entry_id) {
                    Notification::make()
                        ->title('تم تسجيل الاسترداد وترحيل القيد')
                        ->body('رقم السند: '.$rf->number)
                        ->success()
                        ->send();
                } else {
                    Notification::make()
                        ->title('تم تسجيل الاسترداد كمسودة')
                        ->warning()
                        ->send();
                }
            });
    }
}
