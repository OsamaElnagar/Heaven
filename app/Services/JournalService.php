<?php

namespace App\Services;

use App\Enums\JournalEntrySourceType;
use App\Enums\JournalEntryStatus;
use App\Models\AccountOpeningBalance;
use App\Models\DocumentSequence;
use App\Models\FiscalYear;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JournalService
{
    public function post(string $sourceType, int $sourceId, ?array $lines = null, ?string $description = null, ?string $entryDate = null, ?int $fiscalYearId = null, ?string $number = null): JournalEntry
    {
        return DB::transaction(function () use ($sourceType, $sourceId, $lines, $description, $entryDate, $fiscalYearId, $number) {
            $entry = JournalEntry::firstOrNew([
                'source_type' => $sourceType,
                'source_id' => $sourceId,
            ]);

            if ($entry->exists && $entry->status === JournalEntryStatus::POSTED) {
                throw new \RuntimeException('هذا القيد مرحّل بالفعل.');
            }

            $entry->fill([
                'entry_date' => $entryDate ?? now()->toDateString(),
                'status' => JournalEntryStatus::POSTED,
                'posted_by' => Auth::id() ?? 1,
                'posted_at' => now(),
                'description' => $description ?? $this->getDefaultDescription($sourceType),
            ]);

            if (! $entry->exists) {
                $entry->number = $number ?? $this->generateNumber($sourceType, $fiscalYearId);
                $entry->fiscal_year_id = $fiscalYearId ?? $this->currentFiscalYearId();
                $entry->created_by = Auth::id() ?? 1;
            }

            $entry->save();

            if ($lines) {
                $entry->lines()->delete();

                foreach ($lines as $index => $line) {
                    if (isset($line['type']) && isset($line['amount'])) {
                        $type = $line['type'] instanceof \BackedEnum ? $line['type']->value : $line['type'];
                        $amount = (int) $line['amount'];

                        if ($type === 'debit') {
                            $line['debit_amount'] = $amount;
                            $line['credit_amount'] = 0;
                        } else {
                            $line['debit_amount'] = 0;
                            $line['credit_amount'] = $amount;
                        }
                        unset($line['type'], $line['amount']);
                    }

                    JournalLine::create(array_merge($line, [
                        'journal_entry_id' => $entry->id,
                        'sort_order' => $index,
                    ]));
                }
            }

            if (! $entry->isBalanced()) {
                throw new \RuntimeException('القيد غير متوازن. مجموع المدين يجب أن يساوي مجموع الدائن.');
            }

            return $entry;
        });
    }

    public function reverse(int $entryId): JournalEntry
    {
        return DB::transaction(function () use ($entryId) {
            $original = JournalEntry::lockForUpdate()->findOrFail($entryId);

            if ($original->status !== JournalEntryStatus::POSTED) {
                throw new \RuntimeException('يمكن عكس القيود المرحّلة فقط.');
            }

            if ($original->reversed_by_entry_id) {
                throw new \RuntimeException('القيد معكوس بالفعل.');
            }

            $reversal = JournalEntry::create([
                'number' => $this->generateNumber('journal_entry'),
                'fiscal_year_id' => $original->fiscal_year_id,
                'entry_date' => now(),
                'status' => JournalEntryStatus::POSTED,
                'source_type' => JournalEntrySourceType::REVERSAL,
                'source_id' => $original->id,
                'description' => 'عكس القيد #'.$original->number.' - '.$original->description,
                'reversal_of_entry_id' => $original->id,
                'created_by' => Auth::id() ?? 1,
                'posted_by' => Auth::id() ?? 1,
                'posted_at' => now(),
            ]);

            foreach ($original->lines as $line) {
                JournalLine::create([
                    'journal_entry_id' => $reversal->id,
                    'account_id' => $line->account_id,
                    'debit_amount' => $line->credit_amount,
                    'credit_amount' => $line->debit_amount,
                    'description' => 'عكس '.$line->description,
                    'sort_order' => $line->sort_order,
                    'client_id' => $line->client_id,
                    'supplier_id' => $line->supplier_id,
                    'employee_id' => $line->employee_id,
                    'safe_id' => $line->safe_id,
                    'bank_account_id' => $line->bank_account_id,
                ]);
            }

            $original->update([
                'status' => JournalEntryStatus::REVERSED,
                'reversed_by_entry_id' => $reversal->id,
            ]);

            return $reversal;
        });
    }

    public function postOpeningBalances(int $fiscalYearId): JournalEntry
    {
        return DB::transaction(function () use ($fiscalYearId) {
            $balances = AccountOpeningBalance::where('fiscal_year_id', $fiscalYearId)
                ->with('account')
                ->get();

            if ($balances->isEmpty()) {
                throw new \RuntimeException('لا توجد أرصدة افتتاحية للترحيل.');
            }

            $totalDebit = $balances->sum('debit_amount');
            $totalCredit = $balances->sum('credit_amount');

            if ($totalDebit !== $totalCredit) {
                throw new \RuntimeException(
                    'الأرصدة الافتتاحية غير متوازنة. مجموع المدين: '.$totalDebit.'، مجموع الدائن: '.$totalCredit
                );
            }

            $existingEntry = JournalEntry::where('source_type', JournalEntrySourceType::OPENING_BALANCE)
                ->where('source_id', $fiscalYearId)
                ->where('status', JournalEntryStatus::POSTED)
                ->exists();

            if ($existingEntry) {
                throw new \RuntimeException('تم ترحيل الأرصدة الافتتاحية لهذه السنة المالية مسبقاً.');
            }

            $fiscalYear = FiscalYear::findOrFail($fiscalYearId);

            $entry = JournalEntry::create([
                'number' => $this->generateNumber('opening_balance'),
                'fiscal_year_id' => $fiscalYearId,
                'entry_date' => $fiscalYear->starts_at,
                'status' => JournalEntryStatus::POSTED,
                'source_type' => JournalEntrySourceType::OPENING_BALANCE,
                'source_id' => $fiscalYearId,
                'description' => 'أرصدة أول المدة للسنة المالية '.$fiscalYear->name,
                'created_by' => Auth::id() ?? 1,
                'posted_by' => Auth::id() ?? 1,
                'posted_at' => now(),
            ]);

            foreach ($balances as $index => $balance) {
                JournalLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $balance->account_id,
                    'debit_amount' => $balance->debit_amount,
                    'credit_amount' => $balance->credit_amount,
                    'description' => 'رصيد افتتاحي - '.($balance->account?->name ?? ''),
                    'sort_order' => $index,
                ]);
            }

            return $entry;
        });
    }

    public function duplicateAsDraft(int $entryId): JournalEntry
    {
        $original = JournalEntry::with('lines')->findOrFail($entryId);

        $draft = JournalEntry::create([
            'number' => $this->generateNumber('journal_entry'),
            'fiscal_year_id' => $original->fiscal_year_id,
            'entry_date' => now(),
            'status' => JournalEntryStatus::DRAFT,
            'source_type' => 'manual',
            'source_id' => null,
            'description' => 'نسخة من القيد #'.$original->number,
            'created_by' => Auth::id() ?? 1,
        ]);

        foreach ($original->lines as $line) {
            JournalLine::create([
                'journal_entry_id' => $draft->id,
                'account_id' => $line->account_id,
                'debit_amount' => $line->debit_amount,
                'credit_amount' => $line->credit_amount,
                'description' => $line->description,
                'sort_order' => $line->sort_order,
                'client_id' => $line->client_id,
                'supplier_id' => $line->supplier_id,
                'employee_id' => $line->employee_id,
                'safe_id' => $line->safe_id,
                'bank_account_id' => $line->bank_account_id,
            ]);
        }

        return $draft;
    }

    public function generateNumber(string $type, ?int $fiscalYearId = null): string
    {
        $type = 'journal_entry';
        $prefix = 'JE';

        $fiscalYearId ??= $this->currentFiscalYearId();
        $year = $fiscalYearId
            ? FiscalYear::find($fiscalYearId)?->starts_at?->year ?? now()->year
            : now()->year;

        return DB::transaction(function () use ($type, $fiscalYearId, $prefix, $year) {
            $seq = DocumentSequence::where('document_type', $type)
                ->where('fiscal_year_id', $fiscalYearId)
                ->lockForUpdate()
                ->first();

            if (! $seq) {
                $seq = DocumentSequence::create([
                    'document_type' => $type,
                    'fiscal_year_id' => $fiscalYearId,
                    'prefix' => $prefix,
                    'last_number' => 0,
                    'padding' => 5,
                ]);
            }

            do {
                $seq->increment('last_number');
                $number = $prefix.'-'.$year.'-'.str_pad($seq->last_number, $seq->padding, '0', STR_PAD_LEFT);
            } while (JournalEntry::where('number', $number)->exists());

            return $number;
        });
    }

    private function currentFiscalYearId(): ?int
    {
        return FiscalYear::where('status', 'open')->value('id');
    }

    private function getDefaultDescription(string $sourceType): string
    {
        return match ($sourceType) {
            'expense' => 'قيد مصروف',
            'receipt_voucher' => 'قيد سند قبض',
            'payment_voucher' => 'قيد سند صرف',
            'refund_voucher' => 'قيد سند استرداد',
            'journal_entry' => 'قيد يومية',
            'opening_balance' => 'أرصدة أول المدة',
            default => 'قيد ',
        };
    }
}
