<?php

namespace App\Observers;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Payment;

class PaymentObserver
{
    /**
     * Handle the Payment "created" event.
     */
    public function created(Payment $payment): void
    {
        $this->recalculateBooking($payment->booking);
    }

    /**
     * Handle the Payment "deleted" event.
     */
    public function deleted(Payment $payment): void
    {
        $this->recalculateBooking($payment->booking);
    }

    /**
     * Recalculate the booking's paid_amount and derive the appropriate status.
     */
    protected function recalculateBooking(Booking $booking): void
    {
        $paid = $booking->payments()
            ->whereNot('type', 'refund')
            ->sum('amount');

        $refunded = $booking->payments()
            ->where('type', 'refund')
            ->sum('amount');

        $paidAmount = (float) $paid - (float) $refunded;

        $status = match (true) {
            $paidAmount <= 0 => BookingStatus::PENDING,
            default => BookingStatus::CONFIRMED,
        };

        Booking::withoutEvents(function () use ($booking, $paidAmount, $status) {
            $booking->updateQuietly([
                'paid_amount' => $paidAmount,
                'status' => $status,
            ]);
        });
    }
}
