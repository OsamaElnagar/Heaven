<?php

namespace App\Observers;

use App\Models\Agent;
use App\Models\BankAccount;
use App\Models\Branch;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Safe;
use App\Models\Supplier;
use App\Services\Accounting\AccountAutoCreateService;

class PartyAccountObserver
{
    public function __construct(private readonly AccountAutoCreateService $service) {}

    public function creating(Client|Supplier|Employee|Safe|BankAccount|Branch|Agent $party): void
    {
        if ($party->account_id) {
            return;
        }

        $account = $this->service->createFor($party);

        if ($account) {
            $party->account_id = $account->id;
        }
    }
}
