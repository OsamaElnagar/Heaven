<?php

namespace App\Filament\Pages\Reports;

use App\Models\FiscalYear;

trait HasReportFilters
{
    protected function buildFilterText(): ?string
    {
        $filters = [];

        $fiscalYearId = $this->tableFilters['fiscal_year_id']['value'] ?? null;
        if ($fiscalYearId) {
            $fy = FiscalYear::find($fiscalYearId);
            if ($fy) {
                $filters[] = 'السنة المالية: '.$fy->name;
            }
        }

        $dateFrom = $this->tableFilters['date']['date_from'] ?? null;
        $dateTo = $this->tableFilters['date']['date_to'] ?? null;
        if ($dateFrom || $dateTo) {
            $range = 'من '.($dateFrom ?? '...');
            $range .= ' إلى '.($dateTo ?? '...');
            $filters[] = $range;
        }

        $clientId = $this->tableFilters['client_id']['value'] ?? null;
        if ($clientId) {
            $filters[] = 'العميل #'.$clientId;
        }

        $asOfDate = $this->tableFilters['report_date']['as_of_date'] ?? null;
        if ($asOfDate) {
            $filters[] = 'حتى '.$asOfDate;
        }

        $type = $this->tableFilters['type']['value'] ?? null;
        if ($type) {
            $filters[] = 'النوع: '.$type;
        }

        return $filters !== null && $filters !== [] ? implode(' | ', $filters) : null;
    }
}
