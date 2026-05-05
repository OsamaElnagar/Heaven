<?php

namespace App\Observers;

use App\Enums\BookingStatus;
use App\Enums\VisaStatus;
use App\Models\Booking;
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

        if ($current === BookingStatus::CONFIRMED && $original !== BookingStatus::CONFIRMED->value) {
            $this->incrementReservedSeats($booking);

            if (! Visa::where('booking_id', $booking->id)->exists()) {
                Visa::create([
                    'booking_id' => $booking->id,
                    'status' => VisaStatus::NOT_APPLIED,
                ]);
            }
        }

        if ($original === BookingStatus::CONFIRMED->value && $current === BookingStatus::CANCELLED) {
            $this->decrementReservedSeats($booking);
        }
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
