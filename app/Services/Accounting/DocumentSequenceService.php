<?php

namespace App\Services\Accounting;

use App\Models\DocumentSequence;
use App\Models\FiscalYear;
use Illuminate\Support\Facades\DB;

class DocumentSequenceService
{
    protected const DEFAULT_PREFIXES = [
        'JE' => 'JE-',
        'PV' => 'PV-',
        'RV' => 'RV-',
        'RF' => 'RF-',
        'EX' => 'EX-',
        'SA' => 'SA',
        'PA' => 'PA',
        'EA' => 'EA-',
        'RR' => 'RR-',
        'SUP' => 'SUP-',
        'CLI' => 'CLI-',
        'EMP' => 'EMP-',
        'SAF' => 'SAF-',
        'BNK' => 'BNK-',
        'SUB' => 'SUB-',
        'OB' => 'OB-',
    ];

    protected const DEFAULT_PADDING = [
        'JE' => 5,
        'PV' => 5,
        'RV' => 5,
        'RF' => 5,
        'EX' => 5,
        'SA' => 5,
        'PA' => 5,
        'EA' => 5,
        'RR' => 5,
        'SUP' => 5,
        'CLI' => 5,
        'EMP' => 5,
        'SAF' => 5,
        'BNK' => 5,
        'SUB' => 5,
        'OB' => 5,
    ];

    public function getNextNumber(string $documentType, int $fiscalYearId): string
    {
        return DB::transaction(function () use ($documentType, $fiscalYearId) {
            $sequence = DocumentSequence::firstOrCreate(
                [
                    'document_type' => $documentType,
                    'fiscal_year_id' => $fiscalYearId,
                ],
                [
                    'prefix' => $this->getPrefix($documentType),
                    'last_number' => 0,
                    'padding' => $this->getPadding($documentType),
                ]
            );

            $this->incrementSafely($sequence);

            return $sequence->prefix.str_pad(
                $sequence->last_number,
                $sequence->padding,
                '0',
                STR_PAD_LEFT
            );
        });
    }

    public function getNextNumberWithYear(string $documentType): string
    {
        return DB::transaction(function () use ($documentType) {
            $fiscalYear = $this->currentFiscalYear();

            if (! $fiscalYear) {
                throw new \RuntimeException(
                    'Cannot generate a document number: no open fiscal year exists.'
                );
            }

            $prefix = $this->getPrefix($documentType);
            $year = $fiscalYear->starts_at->format('Y');

            $sequence = DocumentSequence::firstOrCreate(
                [
                    'document_type' => $documentType,
                    'fiscal_year_id' => $fiscalYear->id,
                ],
                [
                    'prefix' => $prefix,
                    'last_number' => 0,
                    'padding' => $this->getPadding($documentType),
                ]
            );

            $this->incrementSafely($sequence);

            return $prefix.$year.'-'.str_pad(
                $sequence->last_number,
                $sequence->padding,
                '0',
                STR_PAD_LEFT
            );
        });
    }

    public function getCurrentNumber(string $documentType, int $fiscalYearId): ?string
    {
        $sequence = DocumentSequence::where('document_type', $documentType)
            ->where('fiscal_year_id', $fiscalYearId)
            ->first();

        if (! $sequence || $sequence->last_number === 0) {
            return null;
        }

        return $sequence->prefix.str_pad(
            $sequence->last_number,
            $sequence->padding,
            '0',
            STR_PAD_LEFT
        );
    }

    public function resetSequence(string $documentType, int $fiscalYearId, int $startFrom = 0): void
    {
        DocumentSequence::updateOrCreate(
            [
                'document_type' => $documentType,
                'fiscal_year_id' => $fiscalYearId,
            ],
            [
                'prefix' => $this->getPrefix($documentType),
                'last_number' => $startFrom,
                'padding' => $this->getPadding($documentType),
            ]
        );
    }

    public function setPrefix(string $documentType, int $fiscalYearId, string $prefix): void
    {
        DocumentSequence::where('document_type', $documentType)
            ->where('fiscal_year_id', $fiscalYearId)
            ->update(['prefix' => $prefix]);
    }

    public function initializeForFiscalYear(FiscalYear $fiscalYear): void
    {
        $fiscalYearId = $fiscalYear->id;

        foreach (array_keys(self::DEFAULT_PREFIXES) as $docType) {
            DocumentSequence::firstOrCreate(
                [
                    'document_type' => $docType,
                    'fiscal_year_id' => $fiscalYearId,
                ],
                [
                    'prefix' => $this->getPrefix($docType),
                    'last_number' => 0,
                    'padding' => $this->getPadding($docType),
                ]
            );
        }
    }

    public function duplicateToNewFiscalYear(
        FiscalYear $fromFiscalYear,
        FiscalYear $toFiscalYear
    ): void {
        $sequences = DocumentSequence::where('fiscal_year_id', $fromFiscalYear->id)->get();

        foreach ($sequences as $sequence) {
            DocumentSequence::firstOrCreate(
                [
                    'document_type' => $sequence->document_type,
                    'fiscal_year_id' => $toFiscalYear->id,
                ],
                [
                    'prefix' => $this->getPrefix($sequence->document_type),
                    'last_number' => 0,
                    'padding' => $sequence->padding,
                ]
            );
        }
    }

    protected function getPrefix(string $documentType): string
    {
        return self::DEFAULT_PREFIXES[$documentType] ?? $documentType.'-';
    }

    protected function getPadding(string $documentType): int
    {
        return self::DEFAULT_PADDING[$documentType] ?? 5;
    }

    protected function incrementSafely(DocumentSequence $sequence): void
    {
        $max = (int) str_repeat('9', $sequence->padding);

        if ($sequence->last_number >= $max) {
            throw new DocumentSequenceOverflowException(
                "Sequence for document type '{$sequence->document_type}' in fiscal year "
                .$sequence->fiscal_year_id.' has reached its maximum of '.$max.'.'
            );
        }

        $sequence->increment('last_number');
    }

    private function currentFiscalYear(): ?FiscalYear
    {
        return FiscalYear::where('status', 'open')
            ->orderBy('starts_at', 'desc')
            ->first();
    }
}
