<?php

namespace App\Services;

use App\Enums\PackageType;
use App\Enums\PaymentType;
use App\Models\Client;
use App\Models\Package;
use App\Models\Trip;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Revenue report for a given year, optionally filtered by package type.
     *
     * @return array{total_bookings: int, total_collected: float, total_outstanding: float, packages: array}
     */
    public function revenueReport(int $year, ?PackageType $type = null): array
    {
        $query = Package::where('season_year', $year)
            ->with('bookings.payments');

        if ($type !== null) {
            $query->where('type', $type);
        }

        $packages = $query->get();

        $totalBookings = 0;
        $totalCollected = 0.0;
        $totalOutstanding = 0.0;
        $packageDetails = [];

        foreach ($packages as $package) {
            $bookings = $package->bookings;
            $collected = 0.0;
            $outstanding = 0.0;

            foreach ($bookings as $booking) {
                $paid = (float) $booking->payments
                    ->reject(fn ($p) => $p->type === PaymentType::REFUND)
                    ->sum('amount');
                $refunded = (float) $booking->payments
                    ->filter(fn ($p) => $p->type === PaymentType::REFUND)
                    ->sum('amount');
                $netPaid = $paid - $refunded;
                $netPrice = (float) $booking->net_price;

                $collected += $netPaid;
                $outstanding += max($netPrice - $netPaid, 0);
            }

            $totalBookings += $bookings->count();
            $totalCollected += $collected;
            $totalOutstanding += $outstanding;

            $packageDetails[] = [
                'package_id' => $package->id,
                'package_name' => $package->name,
                'type' => $package->type,
                'bookings' => $bookings->count(),
                'collected' => $collected,
                'outstanding' => $outstanding,
            ];
        }

        return [
            'total_bookings' => $totalBookings,
            'total_collected' => $totalCollected,
            'total_outstanding' => $totalOutstanding,
            'packages' => $packageDetails,
        ];
    }

    /**
     * Visa status dashboard for a trip.
     *
     * @return array<string, int>
     */
    public function visaDashboard(Trip $trip): array
    {
        return DB::table('visas')
            ->join('bookings', 'visas.booking_id', '=', 'bookings.id')
            ->where('bookings.trip_id', $trip->id)
            ->selectRaw('visas.status, COUNT(*) as count')
            ->groupBy('visas.status')
            ->pluck('count', 'status')
            ->toArray();
    }

    /**
     * Occupancy report for a trip — room fill rates per hotel.
     *
     * @return array<int, array{hotel: string, rooms: int, capacity: int, occupied: int, fill_rate: float}>
     */
    public function occupancyReport(Trip $trip): array
    {
        return $trip->rooms()
            ->with('hotel')
            ->get()
            ->groupBy('hotel_id')
            ->map(function ($rooms) {
                $hotel = $rooms->first()->hotel;

                return [
                    'hotel' => $hotel->name,
                    'city' => $hotel->city,
                    'rooms' => $rooms->count(),
                    'capacity' => $rooms->sum('capacity'),
                    'occupied' => $rooms->sum('occupied'),
                    'fill_rate' => $rooms->sum('capacity') > 0
                        ? round(($rooms->sum('occupied') / $rooms->sum('capacity')) * 100, 2)
                        : 0,
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Full booking and payment history for a client (كشف حساب).
     *
     * @return array{client: array, bookings: array}
     */
    public function clientStatement(Client $client): array
    {
        $client->load('bookings.payments', 'bookings.package', 'bookings.visa');

        $bookings = $client->bookings->map(function ($booking) {
            $total = (float) $booking->net_price;
            $paid = (float) $booking->payments
                ->reject(fn ($p) => $p->type === PaymentType::REFUND)
                ->sum('amount');
            $refunded = (float) $booking->payments
                ->filter(fn ($p) => $p->type === PaymentType::REFUND)
                ->sum('amount');

            return [
                'booking_id' => $booking->id,
                'reference' => $booking->reference,
                'package' => $booking->package?->name,
                'status' => $booking->status,
                'total' => $total,
                'paid' => $paid,
                'remaining' => max($total - $paid + $refunded, 0),
                'refunded' => $refunded,
                'visa_status' => $booking->visa?->status,
                'payments' => $booking->payments->toArray(),
            ];
        });

        return [
            'client' => $client->toArray(),
            'bookings' => $bookings->toArray(),
        ];
    }
}
