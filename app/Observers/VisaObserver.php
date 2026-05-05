<?php

namespace App\Observers;

use App\Enums\VisaStatus;
use App\Models\Visa;
use Carbon\Carbon;

class VisaObserver
{
    /**
     * Handle the Visa "updating" event.
     */
    public function updating(Visa $visa): void
    {
        if ($visa->isDirty('status')) {
            if ($visa->status === VisaStatus::APPROVED && $visa->approved_at === null) {
                $visa->approved_at = Carbon::today();
            }

            if ($visa->status === VisaStatus::APPLIED && $visa->applied_at === null) {
                $visa->applied_at = Carbon::today();
            }
        }
    }
}
