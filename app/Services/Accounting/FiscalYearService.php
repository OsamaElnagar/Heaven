<?php

namespace App\Services\Accounting;

use App\Enums\FiscalYearStatus;
use App\Models\FiscalYear;
use Illuminate\Support\Facades\DB;

class FiscalYearService
{
    public function create(array $data): FiscalYear
    {
        $this->validateNoOverlapping($data['starts_at'], $data['ends_at']);

        if (($data['status'] ?? 'open') === 'open') {
            $this->ensureSingleOpenFiscalYear();
        }

        return FiscalYear::create($data);
    }

    public function update(FiscalYear $fiscalYear, array $data): FiscalYear
    {
        if (isset($data['starts_at']) || isset($data['ends_at'])) {
            $this->validateNoOverlapping(
                $data['starts_at'] ?? $fiscalYear->starts_at,
                $data['ends_at'] ?? $fiscalYear->ends_at,
                $fiscalYear->id
            );
        }

        $fiscalYear->update($data);

        return $fiscalYear->fresh();
    }

    public function close(FiscalYear $fiscalYear, int $closedById): FiscalYear
    {
        $this->validateCanClose($fiscalYear);

        return DB::transaction(function () use ($fiscalYear, $closedById) {
            $fiscalYear->update([
                'status' => 'closed',
                'closed_at' => now(),
                'closed_by' => $closedById,
            ]);

            app(DocumentSequenceService::class)->duplicateToNewFiscalYear(
                $fiscalYear,
                $this->getOrCreateNextFiscalYear($fiscalYear)
            );

            return $fiscalYear->fresh();
        });
    }

    public function reopen(FiscalYear $fiscalYear): FiscalYear
    {
        if ($fiscalYear->status !== FiscalYearStatus::CLOSED) {
            throw new \InvalidArgumentException('لا يمكن إعادة فتح السنة المالية إلا إذا كانت مغلقة.');
        }

        $fiscalYear->update([
            'status' => 'open',
            'closed_at' => null,
            'closed_by' => null,
        ]);

        return $fiscalYear->fresh();
    }

    public function getOpen(): ?FiscalYear
    {
        return FiscalYear::where('status', 'open')
            ->orderBy('starts_at', 'desc')
            ->first();
    }

    public function getCurrent(): ?FiscalYear
    {
        $today = now()->toDateString();

        return FiscalYear::where('starts_at', '<=', $today)
            ->where('ends_at', '>=', $today)
            ->where('status', 'open')
            ->first();
    }

    public function findByDate(string $date, ?string $status = 'open'): ?FiscalYear
    {
        $query = FiscalYear::where('starts_at', '<=', $date)
            ->where('ends_at', '>=', $date);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->first();
    }

    public function getNext(FiscalYear $fiscalYear): ?FiscalYear
    {
        return FiscalYear::where('starts_at', '>', $fiscalYear->ends_at)
            ->orderBy('starts_at')
            ->first();
    }

    public function getPrevious(FiscalYear $fiscalYear): ?FiscalYear
    {
        return FiscalYear::where('ends_at', '<', $fiscalYear->starts_at)
            ->orderBy('ends_at', 'desc')
            ->first();
    }

    protected function ensureSingleOpenFiscalYear(): void
    {
        $openYear = $this->getOpen();

        if ($openYear) {
            throw new \InvalidArgumentException(
                'يوجد سنة مالية مفتوحة بالفعل ('.$openYear->name.'). يرجى إقفالها قبل فتح سنة جديدة.'
            );
        }
    }

    protected function validateNoOverlapping(
        string $startsAt,
        string $endsAt,
        ?int $excludeId = null
    ): void {
        $query = FiscalYear::where(function ($q) use ($startsAt, $endsAt) {
            $q->where(function ($q2) use ($startsAt) {
                $q2->where('starts_at', '<=', $startsAt)
                    ->where('ends_at', '>=', $startsAt);
            })->orWhere(function ($q2) use ($endsAt) {
                $q2->where('starts_at', '<=', $endsAt)
                    ->where('ends_at', '>=', $endsAt);
            })->orWhere(function ($q2) use ($startsAt, $endsAt) {
                $q2->where('starts_at', '>=', $startsAt)
                    ->where('ends_at', '<=', $endsAt);
            });
        });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw new \InvalidArgumentException(
                'تاريخ السنة المالية يتداخل مع سنة مالية موجودة مسبقاً.'
            );
        }
    }

    protected function validateCanClose(FiscalYear $fiscalYear): void
    {
        if ($fiscalYear->status === FiscalYearStatus::CLOSED) {
            throw new \InvalidArgumentException('السنة المالية مغلقة بالفعل.');
        }

        $draftCount = $fiscalYear->journalEntries()->where('status', 'draft')->count();

        if ($draftCount > 0) {
            throw new \InvalidArgumentException(
                'لا يمكن إقفال السنة المالية. يوجد '.$draftCount.' قيد (قيود) لم ترحّل بعد. يرجى ترحيلها أو حذفها قبل الإقفال.'
            );
        }
    }

    protected function getOrCreateNextFiscalYear(FiscalYear $fiscalYear): FiscalYear
    {
        $nextStart = $fiscalYear->ends_at->addDay();
        $nextEnd = $fiscalYear->ends_at->addYear();

        $existing = $this->findByDate($nextStart->toDateString());

        if ($existing) {
            return $existing;
        }

        return FiscalYear::create([
            'name' => 'FY '.$nextEnd->format('Y'),
            'starts_at' => $nextStart,
            'ends_at' => $nextEnd,
            'status' => 'open',
        ]);
    }
}
