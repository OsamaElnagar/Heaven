<?php

namespace App\Actions\Statements;

use App\Models\JournalLine;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class BuildJournalStatementRowsAction
{
    /**
     * Build statement rows for a party account (client, supplier, employee, safe, bank).
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function execute(
        int $accountId,
        ?Carbon $from = null,
        ?Carbon $to = null,
        ?int $openingBalance = null,
    ): Collection {
        $opening = $openingBalance ?? $this->getBalanceBefore($accountId, $from);

        $query = JournalLine::query()
            ->with(['journalEntry', 'account'])
            ->where('account_id', $accountId)
            ->whereHas('journalEntry', fn ($q) => $q->where('status', 'posted'))
            ->when($from, fn ($q) => $q->whereHas('journalEntry', fn ($sub) => $sub->whereDate('entry_date', '>=', $from)))
            ->when($to, fn ($q) => $q->whereHas('journalEntry', fn ($sub) => $sub->whereDate('entry_date', '<=', $to)))
            ->join('journal_entries', 'journal_lines.journal_entry_id', '=', 'journal_entries.id')
            ->orderBy('journal_entries.entry_date')
            ->orderBy('journal_entries.id')
            ->orderBy('journal_lines.sort_order')
            ->select('journal_lines.*')
            ->get();

        $balance = $opening;
        $rows = collect();

        foreach ($query as $line) {
            $balance += (int) $line->debit_amount - (int) $line->credit_amount;
            $rows->push([
                'line_id' => $line->id,
                'date' => $line->journalEntry->entry_date,
                'entry_number' => $line->journalEntry->number,
                'description' => $line->description ?? $line->journalEntry->description ?? '',
                'reference' => $line->journalEntry->source_type?->value,
                'debit' => (int) $line->debit_amount,
                'credit' => (int) $line->credit_amount,
                'balance' => $balance,
                'journal_entry_id' => $line->journalEntry->id,
            ]);
        }

        return $rows;
    }

    public function getBalanceBefore(int $accountId, ?Carbon $beforeDate = null): int
    {
        if ($beforeDate === null) {
            return 0;
        }

        $result = JournalLine::query()
            ->where('account_id', $accountId)
            ->whereHas('journalEntry', fn ($q) => $q->where('status', 'posted'))
            ->whereHas('journalEntry', fn ($sub) => $sub->whereDate('entry_date', '<', $beforeDate))
            ->selectRaw('COALESCE(SUM(debit_amount), 0) - COALESCE(SUM(credit_amount), 0) as balance')
            ->value('balance');

        return (int) $result;
    }
}
