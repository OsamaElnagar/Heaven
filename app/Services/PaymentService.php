<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\PaymentType;
use App\Models\Booking;
use App\Models\Payment;
use InvalidArgumentException;

class PaymentService
{
    /**
     * Record a payment against a booking.
     */
    public function recordPayment(Booking $booking, array $data): Payment
    {
        if (($data['amount'] ?? 0) <= 0) {
            throw new InvalidArgumentException('Payment amount must be greater than zero.');
        }

        if (in_array($booking->status, [BookingStatus::CANCELLED, BookingStatus::REFUNDED], true)) {
            throw new InvalidArgumentException('Cannot record payment on a cancelled or refunded booking.');
        }

        return Payment::create([
            'booking_id' => $booking->id,
            ...$data,
        ]);
    }

    /**
     * Issue a refund against a booking.
     */
    public function issueRefund(Booking $booking, float $amount, string $method): Payment
    {
        $refund = Payment::create([
            'booking_id' => $booking->id,
            'type' => PaymentType::REFUND,
            'method' => $method,
            'amount' => $amount,
            'paid_at' => now(),
        ]);

        $totalPaid = (float) $booking->fresh()->paid_amount;

        if ($totalPaid <= 0) {
            Booking::withoutEvents(function () use ($booking) {
                $booking->updateQuietly(['status' => BookingStatus::REFUNDED]);
            });
        }

        return $refund;
    }

    /**
     * Get a payment summary for a booking.
     *
     * @return array{total: float, paid: float, remaining: float, refunded: float, payment_history: array}
     */
    public function getPaymentSummary(Booking $booking): array
    {
        $booking->load('payments');

        $total = (float) $booking->net_price;
        $paid = (float) $booking->payments
            ->whereNot('type', PaymentType::REFUND->value)
            ->sum('amount');
        $refunded = (float) $booking->payments
            ->where('type', PaymentType::REFUND->value)
            ->sum('amount');
        $remaining = max($total - $paid + $refunded, 0);

        return [
            'total' => $total,
            'paid' => $paid,
            'remaining' => $remaining,
            'refunded' => $refunded,
            'payment_history' => $booking->payments->toArray(),
        ];
    }
}
