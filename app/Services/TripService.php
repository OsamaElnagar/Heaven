<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\TripStatus;
use App\Models\Booking;
use App\Models\Trip;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;

class TripService
{
    /**
     * Mark a trip as departed after validating all bookings are confirmed.
     */
    public function depart(Trip $trip): void
    {
        $unconfirmed = $trip->bookings()
            ->whereNot('status', BookingStatus::CONFIRMED)
            ->exists();

        if ($unconfirmed) {
            throw new InvalidArgumentException('All bookings must be confirmed before departure.');
        }

        $trip->update(['status' => TripStatus::DEPARTED]);
    }

    /**
     * Mark a trip as completed and all its bookings as completed.
     */
    public function complete(Trip $trip): void
    {
        $trip->update(['status' => TripStatus::COMPLETED]);

        Booking::withoutEvents(function () use ($trip) {
            $trip->bookings()->update(['status' => BookingStatus::COMPLETED]);
        });
    }

    /**
     * Get the rooming list for a trip, grouped by hotel and city.
     */
    public function getRoomingList(Trip $trip): Collection
    {
        return $trip->rooms()
            ->with(['bookings.client', 'hotel'])
            ->get()
            ->groupBy(fn ($room) => $room->hotel->city)
            ->map(fn ($cityRooms) => $cityRooms->groupBy(fn ($room) => $room->hotel->name));
    }

    /**
     * Get the manifest for a trip with client passport and visa data.
     */
    public function getManifest(Trip $trip): Collection
    {
        return $trip->bookings()
            ->where('status', BookingStatus::CONFIRMED)
            ->with(['client', 'visa'])
            ->get();
    }
}
