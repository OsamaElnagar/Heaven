<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\VisaStatus;
use App\Models\Trip;
use App\Models\Visa;
use Carbon\Carbon;

class VisaService
{
    /**
     * Submit a visa application.
     */
    public function submitApplication(Visa $visa): void
    {
        $visa->update([
            'status' => VisaStatus::APPLIED,
            'applied_at' => Carbon::today(),
        ]);
    }

    /**
     * Mark a visa as approved.
     */
    public function markApproved(Visa $visa, string $visaNumber, Carbon $expiry): void
    {
        $visa->update([
            'status' => VisaStatus::APPROVED,
            'visa_number' => $visaNumber,
            'expiry_date' => $expiry,
        ]);
    }

    /**
     * Mark a visa as rejected.
     */
    public function markRejected(Visa $visa, string $reason): void
    {
        $visa->update([
            'status' => VisaStatus::REJECTED,
            'rejection_reason' => $reason,
        ]);
    }

    /**
     * Bulk submit visa applications for all confirmed bookings on a trip
     * that have not yet applied for a visa.
     */
    public function bulkSubmitForTrip(Trip $trip): void
    {
        $visas = Visa::whereHas('booking', function ($query) use ($trip) {
            $query->where('trip_id', $trip->id)
                ->where('status', BookingStatus::CONFIRMED);
        })->where('status', VisaStatus::NOT_APPLIED)->get();

        foreach ($visas as $visa) {
            $this->submitApplication($visa);
        }
    }
}
