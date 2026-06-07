<?php

namespace App\Filament\Resources\ReceiptVouchers\Actions;

use App\Enums\ExpenseStatus;
use App\Models\Account;
use App\Models\ReceiptVoucher;
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
            ->visible(fn (ReceiptVoucher $record): bool => $record->status === ExpenseStatus::DRAFT)
            ->modalHeading('تأكيد ترحيل سند القبض')
            ->modalDescription('هل أنت متأكد من ترحيل سند القبض؟ لن تتمكن من التعديل بعد الترحيل.')
            ->modalSubmitActionLabel('نعم، رحّل')
            ->requiresConfirmation()
            ->action(function (ReceiptVoucher $record): void {
                DB::transaction(function () use ($record) {
                    $record->refresh();

                    $account1221 = Account::where('code', '1221')->value('id');
                    $account2211 = Account::where('code', '2211')->value('id');

                    $payerAccountId = match ($record->payer_type?->value) {
                        'client' => $record->client?->account_id ?? $account1221,
                        'supplier' => $record->supplier?->account_id ?? $account2211,
                        'employee' => $record->employee?->account_id ?? $account1221,
                        default => $account1221,
                    };

                    if (! $payerAccountId) {
                        throw new \RuntimeException('لم يتم العثور على حساب  للدافع.');
                    }

                    $debitAccountId = match ($record->receipt_method?->value) {
                        'safe' => $record->safe?->account_id,
                        'bank', 'cheque' => $record->bankAccount?->account_id,
                        default => null,
                    };

                    if (! $debitAccountId) {
                        throw new \RuntimeException('لم يتم العثور على حساب  للخزينة أو الحساب البنكي.');
                    }

                    $description = 'تحصيل سند قبض'.($record->description ? ' | '.$record->description : '');

                    $lines = [
                        [
                            'account_id' => $debitAccountId,
                            'debit_amount' => $record->amount,
                            'description' => $description,
                            'safe_id' => $record->safe_id,
                            'bank_account_id' => $record->bank_account_id,
                        ],
                        [
                            'account_id' => $payerAccountId,
                            'credit_amount' => $record->amount,
                            'description' => $description,
                            'client_id' => $record->client_id,
                            'supplier_id' => $record->supplier_id,
                            'employee_id' => $record->employee_id,
                        ],
                    ];

                    app(JournalService::class)->post('receipt_voucher', $record->id, $lines);

                    $record->update([
                        'status' => ExpenseStatus::POSTED,
                        'posted_by' => auth('web')->id(),
                        'posted_at' => now(),
                    ]);
                });

                Notification::make()
                    ->title('تم ترحيل سند القبض بنجاح')
                    ->success()
                    ->send();
            });
    }
}
