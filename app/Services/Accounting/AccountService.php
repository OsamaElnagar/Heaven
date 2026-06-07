<?php

namespace App\Services\Accounting;

use App\Models\Account;
use App\Models\AccountOpeningBalance;
use App\Models\JournalLine;
use Illuminate\Support\Collection;

class AccountService
{
    public function create(array $data): Account
    {
        if (empty($data['code'])) {
            $data['code'] = $this->generateCode($data);
        }

        if ($data['parent_id'] ?? false) {
            $parent = Account::find($data['parent_id']);
            $data['level'] = $parent->level + 1;
        }

        return Account::create($data);
    }

    public function update(Account $account, array $data): Account
    {
        if (isset($data['parent_id']) && $data['parent_id'] !== $account->parent_id) {
            $newParent = Account::find($data['parent_id']);
            $data['level'] = $newParent->level + 1;
        }

        $account->update($data);

        return $account->fresh();
    }

    public function getTree(?string $class = null): Collection
    {
        $query = Account::where('is_active', true)
            ->where('type', 'header')
            ->when($class, fn ($q) => $q->where('class', $class))
            ->with('children.children.children');

        return $query->get();
    }

    public function getDetailAccounts(?string $class = null): Collection
    {
        return Account::where('is_active', true)
            ->where('type', 'detail')
            ->when($class, fn ($q) => $q->where('class', $class))
            ->orderBy('code')
            ->get();
    }

    public function getBalance(int $accountId, ?int $fiscalYearId = null): array
    {
        $account = Account::findOrFail($accountId);

        $openingBalance = $this->getOpeningBalance($accountId, $fiscalYearId);

        $query = JournalLine::where('account_id', $accountId)
            ->whereHas('journalEntry', fn ($q) => $q->where('status', 'posted'));

        if ($fiscalYearId) {
            $query->whereHas('journalEntry', fn ($q) => $q->where('fiscal_year_id', $fiscalYearId));
        }

        $debits = (clone $query)->sum('debit_amount');
        $credits = (clone $query)->sum('credit_amount');

        if ($account->normal_balance === 'debit') {
            $balance = $openingBalance['debit'] - $openingBalance['credit'] + $debits - $credits;
        } else {
            $balance = $openingBalance['credit'] - $openingBalance['debit'] + $credits - $debits;
        }

        return [
            'account_id' => $account->id,
            'account_code' => $account->code,
            'account_name' => $account->name,
            'class' => $account->class,
            'normal_balance' => $account->normal_balance,
            'opening_debit' => $openingBalance['debit'],
            'opening_credit' => $openingBalance['credit'],
            'period_debit' => $debits,
            'period_credit' => $credits,
            'closing_debit' => $openingBalance['debit'] + $debits,
            'closing_credit' => $openingBalance['credit'] + $credits,
            'closing_balance' => $balance,
        ];
    }

    public function setOpeningBalance(
        int $accountId,
        int $fiscalYearId,
        int $debitAmount = 0,
        int $creditAmount = 0
    ): AccountOpeningBalance {
        return AccountOpeningBalance::updateOrCreate(
            [
                'account_id' => $accountId,
                'fiscal_year_id' => $fiscalYearId,
            ],
            [
                'debit_amount' => $debitAmount,
                'credit_amount' => $creditAmount,
            ]
        );
    }

    public function getOpeningBalance(int $accountId, ?int $fiscalYearId = null): array
    {
        $openingBalance = AccountOpeningBalance::where('account_id', $accountId)
            ->when($fiscalYearId, fn ($q) => $q->where('fiscal_year_id', $fiscalYearId))
            ->orderByDesc('fiscal_year_id')
            ->first();

        if ($openingBalance) {
            return [
                'debit' => $openingBalance->debit_amount,
                'credit' => $openingBalance->credit_amount,
            ];
        }

        return ['debit' => 0, 'credit' => 0];
    }

    public function validateAccountCode(string $code, ?int $excludeId = null): bool
    {
        return ! Account::where('code', $code)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->exists();
    }

    public function getAccountByCode(string $code): ?Account
    {
        return Account::where('code', $code)->first();
    }

    public function findOrCreateByCode(string $code, string $name): Account
    {
        return Account::firstOrCreate(
            ['code' => $code],
            [
                'name' => $name,
                'class' => 'expenses',
                'type' => 'detail',
                'normal_balance' => 'debit',
                'is_active' => true,
            ]
        );
    }

    public function deactivate(Account $account): Account
    {
        if ($account->is_system) {
            throw new \InvalidArgumentException('Cannot deactivate system account');
        }

        $account->update(['is_active' => false]);

        $account->children()->update(['is_active' => false]);

        return $account;
    }

    public function getCashAccounts(): Collection
    {
        return Account::where('is_active', true)
            ->where('type', 'detail')
            ->where('class', 'assets')
            ->where('code', 'like', '1101%')
            ->orderBy('code')
            ->get();
    }

    public function getBankAccounts(): Collection
    {
        return Account::where('is_active', true)
            ->where('type', 'detail')
            ->where('class', 'assets')
            ->where('code', 'like', '1102%')
            ->orderBy('code')
            ->get();
    }

    public function getReceivableAccounts(): Collection
    {
        return Account::where('is_active', true)
            ->where('type', 'detail')
            ->where('class', 'assets')
            ->whereIn('code', ['1201', '120101', '120102'])
            ->orderBy('code')
            ->get();
    }

    public function getPayableAccounts(): Collection
    {
        return Account::where('is_active', true)
            ->where('type', 'detail')
            ->where('class', 'liabilities')
            ->whereIn('code', ['2101', '210101'])
            ->orderBy('code')
            ->get();
    }

    protected function generateCode(array $data): string
    {
        if (! empty($data['parent_id'])) {
            $parent = Account::find($data['parent_id']);
            $lastChild = Account::where('parent_id', $data['parent_id'])
                ->orderByDesc('code')
                ->first();

            if ($lastChild) {
                return (int) $lastChild->code + 1;
            }

            return $parent->code.'01';
        }

        $classPrefixes = [
            'assets' => '1',
            'liabilities' => '2',
            'equity' => '3',
            'revenue' => '4',
            'expenses' => '5',
        ];

        $prefix = $classPrefixes[$data['class']] ?? '5';

        $lastCode = Account::where('code', 'like', $prefix.'___')
            ->orderByDesc('code')
            ->first();

        if ($lastCode) {
            return (int) $lastCode->code + 1;
        }

        return $prefix.'001';
    }
}
