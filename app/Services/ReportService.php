<?php

namespace App\Services;

use App\Enums\VisaStatus;
use App\Models\Booking;
use App\Models\Client;
use App\Models\Trip;

class ReportService
{
    /**
     * Get visa status counts for a trip dashboard.
     *
     * @return array<string, int>
     */
    public function visaDashboard(Trip $trip): array
    {
        $visas = Booking::where('trip_id', $trip->id)
            ->where('bookings.status', 'confirmed')
            ->join('visas', 'visas.booking_id', '=', 'bookings.id')
            ->selectRaw('visas.status, count(*) as count')
            ->groupBy('visas.status')
            ->pluck('count', 'visas.status')
            ->toArray();

        $result = [];
        foreach (VisaStatus::cases() as $case) {
            $result[$case->getLabel()] = $visas[$case->value] ?? 0;
        }

        return $result;
    }

    /**
     * Get room occupancy report for a trip.
     *
     * @return array<string, mixed>
     */
    public function occupancyReport(Trip $trip): array
    {
        $rooms = $trip->rooms()->with('hotel')->get();

        $totalCapacity = $rooms->sum('capacity');
        $totalOccupied = $rooms->sum('occupied');

        $byHotel = $rooms->groupBy(fn ($room) => $room->hotel?->name ?? 'غير محدد')
            ->map(fn ($hotelRooms) => [
                'capacity' => $hotelRooms->sum('capacity'),
                'occupied' => $hotelRooms->sum('occupied'),
            ]);

        return [
            'total_capacity' => $totalCapacity,
            'total_occupied' => $totalOccupied,
            'occupancy_rate' => $totalCapacity > 0 ? round(($totalOccupied / $totalCapacity) * 100, 1) : 0,
            'by_hotel' => $byHotel,
        ];
    }

    /**
     * Get client statement with bookings summary.
     */
    public function clientStatement(Client $client): array
    {
        $bookings = $client->bookings()
            ->with(['package', 'trip', 'visa'])
            ->get()
            ->map(fn (Booking $booking) => [
                'id' => $booking->id,
                'reference' => $booking->reference,
                'package_name' => $booking->package?->name ?? '-',
                'trip_name' => $booking->trip?->name ?? '-',
                'status' => $booking->status->getLabel(),
                'status_value' => $booking->status->value,
                'net_price' => $booking->net_price,
                'paid' => $booking->paid_amount,
                'remaining' => max($booking->net_price - $booking->paid_amount, 0),
                'visa_status' => $booking->visa?->status?->getLabel() ?? '-',
            ]);

        return [
            'client' => $client,
            'bookings' => $bookings,
            'total_paid' => $bookings->sum('paid'),
            'total_remaining' => $bookings->sum('remaining'),
        ];
    }
}
