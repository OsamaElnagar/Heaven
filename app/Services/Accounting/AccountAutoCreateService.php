<?php

namespace App\Services\Accounting;

use App\Enums\AccountClass;
use App\Enums\AccountNormalBalance;
use App\Enums\AccountType;
use App\Models\Account;
use App\Models\Agent;
use App\Models\BankAccount;
use App\Models\Branch;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Safe;
use App\Models\Supplier;

class AccountAutoCreateService
{
    private const PARENT_ACCOUNT_CODES = [
        Client::class => '1221',
        Supplier::class => '2211',
        Employee::class => '2231',
        Safe::class => '1231',
        BankAccount::class => '1233',
        Branch::class => '2233',
        Agent::class => '2234',
    ];

    private const PARENT_INHERITANCE = [
        Client::class => ['class' => AccountClass::ASSETS, 'normal' => AccountNormalBalance::DEBIT],
        Supplier::class => ['class' => AccountClass::LIABILITIES, 'normal' => AccountNormalBalance::CREDIT],
        Employee::class => ['class' => AccountClass::LIABILITIES, 'normal' => AccountNormalBalance::CREDIT],
        Safe::class => ['class' => AccountClass::ASSETS, 'normal' => AccountNormalBalance::DEBIT],
        BankAccount::class => ['class' => AccountClass::ASSETS, 'normal' => AccountNormalBalance::DEBIT],
        Branch::class => ['class' => AccountClass::LIABILITIES, 'normal' => AccountNormalBalance::CREDIT],
        Agent::class => ['class' => AccountClass::LIABILITIES, 'normal' => AccountNormalBalance::CREDIT],
    ];

    public function createFor(object $party): ?Account
    {
        if (! array_key_exists($party::class, self::PARENT_ACCOUNT_CODES)) {
            return null;
        }

        $parentCode = self::PARENT_ACCOUNT_CODES[$party::class];
        $parent = Account::where('code', $parentCode)->first();

        if (! $parent) {
            return null;
        }

        $name = $this->buildAccountName($party);
        $code = $this->generateCode($parent->code);

        $inherits = self::PARENT_INHERITANCE[$party::class];

        return Account::create([
            'code' => $code,
            'name' => $name,
            'class' => $inherits['class'],
            'type' => AccountType::DETAIL,
            'normal_balance' => $inherits['normal'],
            'parent_id' => $parent->id,
            'level' => $parent->level + 1,
            'is_active' => true,
            'is_system' => false,
        ]);
    }

    private function buildAccountName(object $party): string
    {
        $code = $party->code ?? '';
        $name = $party->name ?? '';

        return match ($party::class) {
            Client::class, Supplier::class, Employee::class, Safe::class, Branch::class, Agent::class => "{$code} - {$name}",
            BankAccount::class => ($party->bank_name ?? 'Bank').' - '.($party->account_number ?? ''),
        };
    }

    private function generateCode(string $parentCode): string
    {
        $pattern = $parentCode.'%';

        $lastChild = Account::where('code', 'like', $pattern)
            ->where('code', '!=', $parentCode)
            ->orderByDesc('code')
            ->value('code');

        $next = $lastChild ? (int) $lastChild + 1 : (int) ($parentCode.'01');

        return (string) $next;
    }
}
