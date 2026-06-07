<?php

namespace App\Observers;

use App\Models\Account;

class AccountObserver
{
    public function created(Account $account): void
    {
        //
    }

    public function updated(Account $account): void
    {
        //
    }

    public function deleted(Account $account): void
    {
        //
    }

    public function restored(Account $account): void
    {
        //
    }

    public function forceDeleted(Account $account): void
    {
        //
    }

    public function deleting(Account $account): bool
    {
        return ! $account->is_system;
    }

    public function forceDeleting(Account $account): bool
    {
        return ! $account->is_system;
    }
}
