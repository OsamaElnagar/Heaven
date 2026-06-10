<?php

namespace App\Observers;

use App\Enums\ExpenseStatus;
use App\Enums\RefundPartyType;
use App\Enums\VoucherPaymentMethod;
use App\Models\BankAccount;
use App\Models\Booking;
use App\Models\RefundVoucher;
use App\Models\Safe;
use App\Services\JournalService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RefundVoucherObserver
{
    public function created(RefundVoucher $voucher): void
    {
        if ($voucher->isPosted() && ! $voucher->journal_entry_id) {
            $this->postJournalEntry($voucher);
        }
    }

    public function saved(RefundVoucher $voucher): void
    {
        $this->syncBookingPaidAmount($voucher);
    }

    public function deleted(RefundVoucher $voucher): void
    {
        $this->syncBookingPaidAmount($voucher);
    }

    public function updating(RefundVoucher $voucher): void
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

    protected function postJournalEntry(RefundVoucher $voucher): void
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
            $je = app(JournalService::class)->post('refund_voucher', $voucher->id, [
                [
                    'account_id' => $partyAccountId,
                    'debit_amount' => $voucher->amount,
                    'client_id' => $voucher->client_id,
                    'supplier_id' => $voucher->supplier_id,
                    'description' => 'استرداد '.($voucher->party_type?->getLabel() ?? ''),
                ],
                [
                    'account_id' => $cashAccountId,
                    'credit_amount' => $voucher->amount,
                    'safe_id' => $voucher->payment_method === VoucherPaymentMethod::SAFE ? $voucher->safe_id : null,
                    'bank_account_id' => $voucher->payment_method !== VoucherPaymentMethod::SAFE ? $voucher->bank_account_id : null,
                    'description' => 'صرف من الخزينة/البنك',
                ],
            ]);

            $voucher->journal_entry_id = $je->id;
            $voucher->posted_by = Auth::id() ?? 1;
            $voucher->posted_at = now();
            $voucher->saveQuietly();
        } catch (\Throwable $e) {
            Log::warning('RefundVoucherObserver: failed to post journal entry', [
                'voucher_id' => $voucher->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function resolvePartyAccount(RefundVoucher $voucher): ?int
    {
        return match ($voucher->party_type) {
            RefundPartyType::CLIENT => $voucher->client?->account_id,
            RefundPartyType::SUPPLIER => $voucher->supplier?->account_id,
            default => null,
        };
    }

    protected function syncBookingPaidAmount(RefundVoucher $voucher): void
    {
        if (! $voucher->booking_id) {
            return;
        }

        $booking = Booking::find($voucher->booking_id);
        if ($booking) {
            app(BookingObserver::class)->recalculatePaidAmount($booking);
        }
    }

    protected function resolveCashAccount(RefundVoucher $voucher): ?int
    {
        if ($voucher->payment_method === VoucherPaymentMethod::SAFE) {
            return Safe::find($voucher->safe_id)?->account_id;
        }

        return BankAccount::find($voucher->bank_account_id)?->account_id;
    }
}
