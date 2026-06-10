<?php

namespace App\Observers;

use App\Enums\ExpenseStatus;
use App\Enums\PayerType;
use App\Enums\VoucherPaymentMethod;
use App\Models\BankAccount;
use App\Models\Booking;
use App\Models\ReceiptVoucher;
use App\Models\Safe;
use App\Services\JournalService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReceiptVoucherObserver
{
    public function created(ReceiptVoucher $voucher): void
    {
        if ($voucher->isPosted() && ! $voucher->journal_entry_id) {
            $this->postJournalEntry($voucher);
        }
    }

    public function saved(ReceiptVoucher $voucher): void
    {
        $this->syncBookingPaidAmount($voucher);
    }

    public function deleted(ReceiptVoucher $voucher): void
    {
        $this->syncBookingPaidAmount($voucher);
    }

    public function updating(ReceiptVoucher $voucher): void
    {
        if (! $voucher->isDirty('status')) {
            return;
        }

        $original = $voucher->getOriginal('status');
        $newStatus = $voucher->status;

        if ($newStatus !== ExpenseStatus::POSTED) {
            return;
        }
        if ($original === ExpenseStatus::POSTED) {
            return;
        }

        $this->postJournalEntry($voucher);
    }

    protected function postJournalEntry(ReceiptVoucher $voucher): void
    {
        $partyAccountId = $this->resolvePartyAccount($voucher);
        if (! $partyAccountId) {
            return;
        }

        $cashAccountId = $this->resolveCashAccount($voucher);
        if (! $cashAccountId) {
            return;
        }

        try {
            $je = app(JournalService::class)->post('receipt_voucher', $voucher->id, [
                [
                    'account_id' => $cashAccountId,
                    'debit_amount' => $voucher->amount,
                    'safe_id' => $voucher->receipt_method === VoucherPaymentMethod::SAFE ? $voucher->safe_id : null,
                    'bank_account_id' => $voucher->receipt_method !== VoucherPaymentMethod::SAFE ? $voucher->bank_account_id : null,
                    'description' => 'استلام دفعة',
                ],
                [
                    'account_id' => $partyAccountId,
                    'credit_amount' => $voucher->amount,
                    'client_id' => $voucher->client_id,
                    'supplier_id' => $voucher->supplier_id,
                    'employee_id' => $voucher->employee_id,
                    'description' => 'دفعة من '.$voucher->payerLabel(),
                ],
            ]);

            $voucher->journal_entry_id = $je->id;
            $voucher->posted_by = Auth::id() ?? 1;
            $voucher->posted_at = now();
            $voucher->saveQuietly();
        } catch (\Throwable $e) {
            Log::warning('ReceiptVoucherObserver: failed to post journal entry', [
                'voucher_id' => $voucher->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function resolvePartyAccount(ReceiptVoucher $voucher): ?int
    {
        return match ($voucher->payer_type) {
            PayerType::CLIENT => $voucher->client?->account_id,
            PayerType::SUPPLIER => $voucher->supplier?->account_id,
            PayerType::EMPLOYEE => $voucher->employee?->account_id,
            default => null,
        };
    }

    protected function syncBookingPaidAmount(ReceiptVoucher $voucher): void
    {
        if (! $voucher->booking_id) {
            return;
        }

        $booking = Booking::find($voucher->booking_id);
        if ($booking) {
            app(BookingObserver::class)->recalculatePaidAmount($booking);
        }
    }

    protected function resolveCashAccount(ReceiptVoucher $voucher): ?int
    {
        if ($voucher->receipt_method === VoucherPaymentMethod::SAFE) {
            return Safe::find($voucher->safe_id)?->account_id;
        }

        return BankAccount::find($voucher->bank_account_id)?->account_id;
    }
}
