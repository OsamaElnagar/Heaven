<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\RoomType;
use App\Models\Booking;
use App\Models\Client;
use App\Models\Package;
use App\Models\Room;
use InvalidArgumentException;

class BookingService
{
    /**
     * Create a new booking for a client on a package.
     */
    public function createBooking(Client $client, Package $package, array $data): Booking
    {
        if (($package->total_seats - $package->reserved_seats) <= 0) {
            throw new InvalidArgumentException('No available seats on this package.');
        }

        return Booking::create([
            'client_id' => $client->id,
            'package_id' => $package->id,
            ...$data,
        ]);
    }

    /**
     * Cancel a booking with an optional reason.
     */
    public function cancelBooking(Booking $booking, ?string $reason = null): void
    {
        $booking->update([
            'status' => BookingStatus::CANCELLED,
            'notes' => $reason ? ($booking->notes."\nCancelled: {$reason}") : $booking->notes,
        ]);
    }

    /**
     * Assign a room to a booking, validating capacity.
     * Decrements the previous room's occupied count if reassigning.
     */
    public function assignRoom(Booking $booking, Room $room): void
    {
        if ($room->occupied >= $room->capacity) {
            throw new InvalidArgumentException('Room is at full capacity.');
        }

        if ($booking->room_id && $booking->room_id !== $room->id) {
            $this->decrementOccupied($booking->room);
        }

        $booking->update(['room_id' => $room->id]);
        $room->increment('occupied');
    }

    /**
     * Remove the room assignment from a booking, decrementing occupied count.
     */
    public function unassignRoom(Booking $booking): void
    {
        if (! $booking->room_id) {
            return;
        }

        $this->decrementOccupied($booking->room);
        $booking->update(['room_id' => null]);
    }

    /**
     * Safely decrement a room's occupied count (floor at 0).
     */
    protected function decrementOccupied(Room $room): void
    {
        if ($room->occupied > 0) {
            $room->decrement('occupied');
        }
    }

    /**
     * Calculate pricing breakdown for a package and room type.
     *
     * @return array{base_price: float, room_surcharge: float, discount: float, net_price: float}
     */
    public function calculatePricing(Package $package, RoomType $roomType, float $discount = 0): array
    {
        $basePrice = (float) $package->base_price;

        $surcharge = match ($roomType) {
            RoomType::SINGLE => $basePrice * 0.5,
            RoomType::DOUBLE => 0,
            RoomType::TRIPLE => -$basePrice * 0.1,
            RoomType::QUAD => -$basePrice * 0.2,
            RoomType::QUINT => -$basePrice * 0.3,
            RoomType::SEXTUPLE => -$basePrice * 0.4,
        };

        $netPrice = $basePrice + $surcharge - $discount;

        return [
            'base_price' => $basePrice,
            'room_surcharge' => $surcharge,
            'discount' => $discount,
            'net_price' => max($netPrice, 0),
        ];
    }
}
