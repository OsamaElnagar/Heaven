<?php

namespace App\Services;

use App\Enums\VisaStatus;
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
     * Expire visas whose expiry date has passed.
     */
    public function expireOverdueVisas(): int
    {
        return Visa::where('status', VisaStatus::APPROVED)
            ->where('expiry_date', '<', Carbon::today())
            ->update(['status' => VisaStatus::EXPIRED]);
    }
}
