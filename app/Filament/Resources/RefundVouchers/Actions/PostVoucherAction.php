<?php

namespace App\Filament\Resources\RefundVouchers\Actions;

use App\Enums\ExpenseStatus;
use App\Enums\RefundPartyType;
use App\Models\Account;
use App\Models\RefundVoucher;
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
            ->visible(fn (RefundVoucher $record): bool => $record->status === ExpenseStatus::DRAFT)
            ->modalHeading('تأكيد ترحيل سند الاسترداد')
            ->modalDescription('هل أنت متأكد من ترحيل سند الاسترداد؟ لن تتمكن من التعديل بعد الترحيل.')
            ->modalSubmitActionLabel('نعم، رحّل')
            ->requiresConfirmation()
            ->action(function (RefundVoucher $record): void {
                DB::transaction(function () use ($record) {
                    $record->refresh();

                    $account1221 = Account::where('code', '1221')->value('id');
                    $account2211 = Account::where('code', '2211')->value('id');

                    $partyAccountId = null;
                    $partyForeignKey = null;
                    $partyLabel = '';

                    if ($record->party_type === RefundPartyType::CLIENT) {
                        $partyAccountId = $record->client?->account_id ?? $account1221;
                        $partyForeignKey = 'client_id';
                        $partyLabel = 'للعميل';
                    } elseif ($record->party_type === RefundPartyType::SUPPLIER) {
                        $partyAccountId = $record->supplier?->account_id ?? $account2211;
                        $partyForeignKey = 'supplier_id';
                        $partyLabel = 'من المورد';
                    }

                    if (! $partyAccountId) {
                        throw new \RuntimeException('لم يتم العثور على حساب  للطرف.');
                    }

                    $cashAccountId = match ($record->payment_method?->value) {
                        'safe' => $record->safe?->account_id,
                        'bank', 'cheque' => $record->bankAccount?->account_id,
                        default => null,
                    };

                    if (! $cashAccountId) {
                        throw new \RuntimeException('لم يتم العثور على حساب  للخزينة أو الحساب البنكي.');
                    }

                    $description = 'استرداد '.$partyLabel.($record->description ? ' | '.$record->description : '');

                    $lines = match ($record->party_type) {
                        RefundPartyType::CLIENT => [
                            [
                                'account_id' => $partyAccountId,
                                'debit_amount' => $record->amount,
                                'description' => $description,
                                $partyForeignKey => $record->{$record->party_type->value.'_id'},
                            ],
                            [
                                'account_id' => $cashAccountId,
                                'credit_amount' => $record->amount,
                                'description' => $description,
                                'safe_id' => $record->safe_id,
                                'bank_account_id' => $record->bank_account_id,
                            ],
                        ],
                        RefundPartyType::SUPPLIER => [
                            [
                                'account_id' => $cashAccountId,
                                'debit_amount' => $record->amount,
                                'description' => $description,
                                'safe_id' => $record->safe_id,
                                'bank_account_id' => $record->bank_account_id,
                            ],
                            [
                                'account_id' => $partyAccountId,
                                'credit_amount' => $record->amount,
                                'description' => $description,
                                $partyForeignKey => $record->{$record->party_type->value.'_id'},
                            ],
                        ],
                    };

                    app(JournalService::class)->post('refund_voucher', $record->id, $lines);

                    $record->update([
                        'status' => ExpenseStatus::POSTED,
                        'posted_by' => auth('web')->id(),
                        'posted_at' => now(),
                    ]);
                });

                Notification::make()
                    ->title('تم ترحيل سند الاسترداد بنجاح')
                    ->success()
                    ->send();
            });
    }
}
