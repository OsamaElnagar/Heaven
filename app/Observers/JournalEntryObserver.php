<?php

namespace App\Observers;

use App\Enums\JournalEntryStatus;
use App\Models\FiscalYear;
use App\Models\JournalEntry;

class JournalEntryObserver
{
    public function saving(JournalEntry $entry): void
    {
        if ($entry->getOriginal('status') === JournalEntryStatus::POSTED->value
            || $entry->getOriginal('status') === JournalEntryStatus::REVERSED->value) {
            throw new \RuntimeException('لا يمكن تعديل قيد مرحّل أو معكوس.');
        }

        if ($entry->isDirty('status') && $entry->status === JournalEntryStatus::POSTED) {
            if (! $entry->isBalanced()) {
                throw new \RuntimeException('القيد غير متوازن. مجموع المدين يجب أن يساوي مجموع الدائن.');
            }

            if (empty($entry->fiscal_year_id)) {
                throw new \RuntimeException('يجب تحديد السنة المالية للقيد.');
            }

            $fiscalYear = FiscalYear::find($entry->fiscal_year_id);

            if ($fiscalYear && $entry->entry_date) {
                if ($entry->entry_date->lt($fiscalYear->starts_at) || $entry->entry_date->gt($fiscalYear->ends_at)) {
                    throw new \RuntimeException('تاريخ القيد خارج نطاق السنة المالية.');
                }
            }

            $entry->posted_at = now();
        }
    }

    public function deleting(JournalEntry $entry): void
    {
        if ($entry->status === JournalEntryStatus::POSTED
            || $entry->status === JournalEntryStatus::REVERSED) {
            throw new \RuntimeException('لا يمكن حذف قيد مرحّل أو معكوس.');
        }
    }
}
