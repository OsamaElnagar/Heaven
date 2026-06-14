<?php

namespace App\Observers;

use App\Enums\EmployeeAdvanceStatus;
use App\Models\EmployeeAdvance;

class EmployeeAdvanceObserver
{
    public function saving(EmployeeAdvance $advance): void
    {
        if ($advance->repaid_amount >= $advance->amount) {
            $advance->status = EmployeeAdvanceStatus::FULLY_REPAID;
        }
    }
}
