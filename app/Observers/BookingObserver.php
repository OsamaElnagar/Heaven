<?php

namespace App\Observers;

use App\Enums\BookingChannel;
use App\Enums\BookingStatus;
use App\Enums\CommissionStatus;
use App\Enums\ExpenseStatus;
use App\Enums\VisaStatus;
use App\Models\Booking;
use App\Models\Commission;
use App\Models\Package;
use App\Models\Visa;
use Carbon\CarbonImmutable;

class BookingObserver
{
    /**
     * Handle the Booking "creating" event.
     */
    public function creating(Booking $booking): void
    {
        $booking->reference = $this->generateReference();

        if ($booking->net_price === null) {
            $booking->net_price = (float) ($booking->total_price ?? 0) - (float) ($booking->discount ?? 0);
        }

        if ($booking->status === null) {
            $booking->status = BookingStatus::PENDING;
        }
    }

    /**
     * Handle the Booking "updating" event.
     */
    public function updating(Booking $booking): void
    {
        if ($booking->isDirty(['total_price', 'discount'])) {
            $booking->net_price = (float) ($booking->total_price ?? 0) - (float) ($booking->discount ?? 0);
        }
    }

    /**
     * Handle the Booking "saved" event.
     */
    public function saved(Booking $booking): void
    {
        if ($booking->wasChanged('status')) {
            $this->handleStatusChange($booking);
        }
    }

    /**
     * Handle seat reservation changes and visa creation on status change.
     */
    protected function handleStatusChange(Booking $booking): void
    {
        $original = $booking->getOriginal('status');
        $current = $booking->status;

        if ($current === BookingStatus::CONFIRMED && $original?->value !== BookingStatus::CONFIRMED->value) {
            $this->incrementReservedSeats($booking);

            if (! Visa::where('booking_id', $booking->id)->exists()) {
                Visa::create([
                    'booking_id' => $booking->id,
                    'status' => VisaStatus::NOT_APPLIED,
                ]);
            }

            $this->createCommission($booking);
        }

        if ($original?->value === BookingStatus::CONFIRMED->value && $current === BookingStatus::CANCELLED) {
            $this->decrementReservedSeats($booking);
        }
    }

    /**
     * Create a commission record if the booking was made through a branch or agent.
     */
    protected function createCommission(Booking $booking): void
    {
        if ($booking->channel === BookingChannel::DIRECT) {
            return;
        }

        $rate = match ($booking->channel) {
            BookingChannel::BRANCH => $booking->branch?->commission_percentage,
            BookingChannel::AGENT => $booking->agent?->commission_percentage,
            default => 0,
        };

        $rate = (float) ($rate ?? 0);

        if ($rate <= 0) {
            return;
        }

        $amount = ($booking->net_price * $rate) / 100;

        Commission::withoutEvents(function () use ($booking, $rate, $amount) {
            Commission::create([
                'booking_id' => $booking->id,
                'branch_id' => $booking->channel === BookingChannel::BRANCH ? $booking->branch_id : null,
                'agent_id' => $booking->channel === BookingChannel::AGENT ? $booking->agent_id : null,
                'commission_type' => 'percentage',
                'commission_rate' => $rate,
                'amount' => $amount,
                'status' => CommissionStatus::PENDING,
            ]);
        });
    }

    /**
     * Increment the package reserved_seats.
     */
    protected function incrementReservedSeats(Booking $booking): void
    {
        Package::withoutEvents(function () use ($booking) {
            Package::where('id', $booking->package_id)->increment('reserved_seats');
        });
    }

    /**
     * Decrement the package reserved_seats.
     */
    protected function decrementReservedSeats(Booking $booking): void
    {
        Package::withoutEvents(function () use ($booking) {
            Package::where('id', $booking->package_id)
                ->where('reserved_seats', '>', 0)
                ->decrement('reserved_seats');
        });
    }

    /**
     * Recalculate the booking's paid_amount based on posted receipt/refund vouchers.
     *
     * Called externally from ReceiptVoucherObserver and RefundVoucherObserver after
     * a voucher is posted or unposted. This method MUST be invoked via
     * BookingObserver::withoutEvents() from those observers to prevent infinite
     * recursion — voucher changes trigger this recalc, which updates the booking,
     * which would otherwise re-trigger the observer chain.
     *
     * This method intentionally uses `updateQuietly()` inside `withoutEvents()` to
     * skip all BookingObserver hooks (reference generation, net_price recalc, seat
     * tracking). The caller is responsible for ensuring the booking state is
     * consistent before invoking this.
     *
     * @see ReceiptVoucherObserver
     * @see RefundVoucherObserver
     */
    public function recalculatePaidAmount(Booking $booking): void
    {
        if (! $booking) {
            return;
        }

        $received = (int) $booking->receiptVouchers()
            ->where('status', ExpenseStatus::POSTED)
            ->sum('amount');

        $refunded = (int) $booking->refundVouchers()
            ->where('status', ExpenseStatus::POSTED)
            ->sum('amount');

        $netPaid = $received - $refunded;

        $status = match (true) {
            $netPaid <= 0 => BookingStatus::PENDING,
            default => BookingStatus::CONFIRMED,
        };

        Booking::withoutEvents(function () use ($booking, $netPaid, $status) {
            $booking->updateQuietly([
                'paid_amount' => $netPaid,
                'status' => $status,
            ]);
        });
    }

    /**
     * Generate a unique booking reference: BK-{YEAR}-{padded sequence}.
     */
    protected function generateReference(): string
    {
        $year = CarbonImmutable::now()->year;

        $last = Booking::where('reference', 'like', "BK-{$year}-%")
            ->orderByDesc('reference')
            ->lockForUpdate()
            ->first();

        $sequence = $last ? ((int) substr($last->reference, -5)) + 1 : 1;

        return sprintf('BK-%s-%05d', $year, $sequence);
    }
}
