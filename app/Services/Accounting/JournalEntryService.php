<?php

namespace App\Services\Accounting;

use App\Enums\JournalEntryStatus;
use App\Models\Account;
use App\Models\FiscalYear;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class JournalEntryService
{
    public function create(array $data, Collection $lines): JournalEntry
    {
        return DB::transaction(function () use ($data, $lines) {
            $entry = JournalEntry::create([
                'number' => $this->generateNumber($data['fiscal_year_id']),
                'fiscal_year_id' => $data['fiscal_year_id'],
                'entry_date' => $data['entry_date'],
                'description' => $data['description'],
                'source_type' => $data['source_type'] ?? 'manual',
                'source_id' => $data['source_id'] ?? null,
                'reference' => $data['reference'] ?? null,
                'notes' => $data['notes'] ?? null,
                'attachment' => $data['attachment'] ?? null,
                'created_by' => $data['created_by'],
                'status' => 'draft',
            ]);

            foreach ($lines as $index => $lineData) {
                $entry->lines()->create(array_merge($lineData, ['sort_order' => $index]));
            }

            return $entry->load('lines');
        });
    }

    public function post(JournalEntry $entry, int $postedById): JournalEntry
    {
        if ($entry->status !== JournalEntryStatus::DRAFT) {
            throw new \InvalidArgumentException('يجب أن يكون القيد في حالة مسودة ليتم ترحيله.');
        }

        if (! $this->isBalanced($entry)) {
            throw new \InvalidArgumentException('القيد غير متوازن. مجموع المدين يجب أن يساوي مجموع الدائن.');
        }

        $entry->update([
            'status' => 'posted',
            'posted_by' => $postedById,
            'posted_at' => now(),
        ]);

        return $entry->fresh();
    }

    public function reverse(JournalEntry $entry, int $reversedById): JournalEntry
    {
        return DB::transaction(function () use ($entry, $reversedById) {
            $freshEntry = $entry->fresh();

            if ($freshEntry->status !== JournalEntryStatus::POSTED) {
                throw new \InvalidArgumentException('يمكن عكس القيود المرحّلة فقط.');
            }

            if ($freshEntry->reversed_by_entry_id) {
                throw new \InvalidArgumentException('القيد معكوس بالفعل.');
            }

            $reversalEntry = $this->create([
                'fiscal_year_id' => $entry->fiscal_year_id,
                'entry_date' => now()->toDateString(),
                'description' => 'Reversal of '.$entry->number,
                'source_type' => 'reversal',
                'created_by' => $reversedById,
            ], $this->getReversalLines($entry));

            $entry->update([
                'status' => 'reversed',
                'reversed_by_entry_id' => $reversalEntry->id,
            ]);

            $reversalEntry->update(['reversal_of_entry_id' => $entry->id]);

            return $reversalEntry->load('lines');
        });
    }

    public function isBalanced(JournalEntry $entry): bool
    {
        $debitTotal = $entry->lines()->sum('debit_amount');
        $creditTotal = $entry->lines()->sum('credit_amount');

        return $debitTotal === $creditTotal;
    }

    public function getDebitTotal(JournalEntry $entry): int
    {
        return (int) $entry->lines()->sum('debit_amount');
    }

    public function getCreditTotal(JournalEntry $entry): int
    {
        return (int) $entry->lines()->sum('credit_amount');
    }

    public function getAccountBalance(int $accountId, ?int $fiscalYearId = null): array
    {
        $query = JournalLine::where('account_id', $accountId)
            ->whereHas('journalEntry', fn ($q) => $q->where('status', 'posted'));

        if ($fiscalYearId) {
            $query->whereHas('journalEntry', fn ($q) => $q->where('fiscal_year_id', $fiscalYearId));
        }

        $debits = (clone $query)->sum('debit_amount');
        $credits = (clone $query)->sum('credit_amount');

        $account = Account::find($accountId);

        if ($account->normal_balance === 'debit') {
            $balance = $debits - $credits;
        } else {
            $balance = $credits - $debits;
        }

        return [
            'debits' => $debits,
            'credits' => $credits,
            'balance' => $balance,
        ];
    }

    public function getTrialBalance(?int $fiscalYearId = null): Collection
    {
        $query = Account::where('type', 'detail')
            ->where('is_active', true)
            ->with(['journalLines' => function ($q) use ($fiscalYearId) {
                $q->whereHas('journalEntry', function ($q2) use ($fiscalYearId) {
                    $q2->where('status', 'posted');
                    if ($fiscalYearId) {
                        $q2->where('fiscal_year_id', $fiscalYearId);
                    }
                });
            }]);

        return $query->get()->map(function ($account) {
            $debits = $account->journalLines->sum('debit_amount');
            $credits = $account->journalLines->sum('credit_amount');

            return [
                'account_id' => $account->id,
                'account_code' => $account->code,
                'account_name' => $account->name,
                'class' => $account->class,
                'debit' => $debits,
                'credit' => $credits,
            ];
        });
    }

    public function getAccountStatement(
        int $accountId,
        ?int $fiscalYearId = null,
        ?string $startDate = null,
        ?string $endDate = null
    ): array {
        $account = Account::findOrFail($accountId);
        $fiscalYear = $fiscalYearId ? FiscalYear::find($fiscalYearId) : null;

        $openingBalance = $this->getOpeningBalance($accountId, $fiscalYearId, $startDate);

        $query = JournalLine::where('account_id', $accountId)
            ->whereHas('journalEntry', function ($q) use ($fiscalYearId, $startDate, $endDate) {
                $q->where('status', 'posted');
                if ($fiscalYearId) {
                    $q->where('fiscal_year_id', $fiscalYearId);
                }
                if ($startDate) {
                    $q->where('entry_date', '>=', $startDate);
                }
                if ($endDate) {
                    $q->where('entry_date', '<=', $endDate);
                }
            })->with('journalEntry');

        $transactions = $query->orderBy('journalEntry.entry_date')
            ->orderBy('id')
            ->get()
            ->map(function ($line) {
                return [
                    'date' => $line->journalEntry->entry_date,
                    'entry_number' => $line->journalEntry->number,
                    'description' => $line->description ?? $line->journalEntry->description,
                    'debit' => $line->debit_amount,
                    'credit' => $line->credit_amount,
                ];
            });

        return [
            'account' => [
                'id' => $account->id,
                'code' => $account->code,
                'name' => $account->name,
                'class' => $account->class,
                'normal_balance' => $account->normal_balance,
            ],
            'fiscal_year' => $fiscalYear ? ['id' => $fiscalYear->id, 'name' => $fiscalYear->name] : null,
            'period' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
            'opening_balance' => $openingBalance,
            'transactions' => $transactions,
        ];
    }

    protected function getOpeningBalance(int $accountId, ?int $fiscalYearId, ?string $beforeDate = null): array
    {
        $query = JournalLine::where('account_id', $accountId)
            ->whereHas('journalEntry', function ($q) use ($fiscalYearId, $beforeDate) {
                $q->where('status', 'posted');
                if ($fiscalYearId) {
                    $q->where('fiscal_year_id', $fiscalYearId);
                }
                if ($beforeDate) {
                    $q->where('entry_date', '<', $beforeDate);
                }
            });

        $debits = (clone $query)->sum('debit_amount');
        $credits = (clone $query)->sum('credit_amount');

        $account = Account::find($accountId);

        if ($account->normal_balance === 'debit') {
            $balance = $debits - $credits;
        } else {
            $balance = $credits - $debits;
        }

        return [
            'debits' => $debits,
            'credits' => $credits,
            'balance' => $balance,
        ];
    }

    protected function generateNumber(int $fiscalYearId): string
    {
        return app(DocumentSequenceService::class)->getNextNumber('JE', $fiscalYearId);
    }

    protected function getReversalLines(JournalEntry $entry): Collection
    {
        return $entry->lines->map(function ($line) {
            return [
                'account_id' => $line->account_id,
                'debit_amount' => $line->credit_amount,
                'credit_amount' => $line->debit_amount,
                'description' => $line->description,
                'client_id' => $line->client_id,
                'supplier_id' => $line->supplier_id,
                'employee_id' => $line->employee_id,
            ];
        });
    }
}
