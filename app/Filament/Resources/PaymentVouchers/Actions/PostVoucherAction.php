<?php

namespace App\Filament\Resources\PaymentVouchers\Actions;

use App\Enums\ExpenseStatus;
use App\Models\Account;
use App\Models\PaymentVoucher;
use App\Services\JournalService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class PostVoucherAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'postVoucher';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('ترحيل')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->visible(fn (PaymentVoucher $record): bool => $record->status === ExpenseStatus::DRAFT)
            ->modalHeading('تأكيد ترحيل سند الصرف')
            ->modalDescription('هل أنت متأكد من ترحيل سند الصرف؟ لن تتمكن من التعديل بعد الترحيل.')
            ->modalSubmitActionLabel('نعم، رحّل')
            ->requiresConfirmation()
            ->action(function (PaymentVoucher $record): void {
                DB::transaction(function () use ($record) {
                    $record->refresh();

                    $account2211 = Account::where('code', '2211')->value('id');
                    $account1221 = Account::where('code', '1221')->value('id');
                    $account2222 = Account::where('code', '2222')->value('id');

                    $payeeAccountId = match ($record->payee_type?->value) {
                        'supplier' => $record->supplier?->account_id ?? $account2211,
                        'client' => $record->client?->account_id ?? $account1221,
                        'employee' => $record->employee?->account_id ?? $account2211,
                        default => $record->expense?->account_id ?? $account2211,
                    };

                    if (! $payeeAccountId) {
                        throw new \RuntimeException('لم يتم العثور على حساب  للمستلم.');
                    }

                    $creditAccountId = match ($record->payment_method?->value) {
                        'safe' => $record->safe?->account_id,
                        'bank', 'cheque' => $record->bankAccount?->account_id,
                        default => null,
                    };

                    if (! $creditAccountId) {
                        throw new \RuntimeException('لم يتم العثور على حساب  للخزينة أو الحساب البنكي.');
                    }

                    $netAmount = $record->amount - $record->withholding_amount;
                    $description = 'سداد سند صرف'.($record->description ? ' | '.$record->description : '');

                    $lines[] = [
                        'account_id' => $payeeAccountId,
                        'debit_amount' => $record->amount,
                        'description' => $description,
                        'supplier_id' => $record->supplier_id,
                        'client_id' => $record->client_id,
                        'employee_id' => $record->employee_id,
                    ];

                    $lines[] = [
                        'account_id' => $creditAccountId,
                        'credit_amount' => $netAmount,
                        'description' => $description,
                        'safe_id' => $record->safe_id,
                        'bank_account_id' => $record->bank_account_id,
                    ];

                    if ($record->withholding_amount > 0 && $account2222) {
                        $lines[] = [
                            'account_id' => $account2222,
                            'credit_amount' => $record->withholding_amount,
                            'description' => 'خصم وإضافة مستقطع',
                        ];
                    }

                    app(JournalService::class)->post('payment_voucher', $record->id, $lines);

                    $record->update([
                        'status' => ExpenseStatus::POSTED,
                        'posted_by' => auth('web')->id(),
                        'posted_at' => now(),
                    ]);
                });

                Notification::make()
                    ->title('تم ترحيل سند الصرف بنجاح')
                    ->success()
                    ->send();
            });
    }
}
