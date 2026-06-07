<?php

namespace App\Services;

use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;
use Mccarlosen\LaravelMpdf\LaravelMpdf;

class PdfService
{
    public function generateStatementPdf(
        string $type,
        string $entityName,
        array $statement,
        array $entries
    ): LaravelMpdf {
        return PDF::loadView('pdf.statement', [
            'type' => $type,
            'entityName' => $entityName,
            'statement' => $statement,
            'entries' => $entries,
            'generatedAt' => now()->format('Y-m-d h:i A'),
            'storeName' => config('app.name'),
        ], [], [
            'title' => $statement['title'] ?? 'كشف حساب',
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_left' => 10,
            'margin_right' => 10,
        ]);
    }

    public function generateReportPdf(
        string $title,
        array $columns,
        array $rows,
        array $summaries = [],
        ?string $filters = null,
    ): LaravelMpdf {
        return PDF::loadView('pdf.report', [
            'title' => $title,
            'columns' => $columns,
            'rows' => $rows,
            'summaries' => $summaries,
            'filters' => $filters,
            'generatedAt' => now()->format('Y-m-d h:i A'),
            'storeName' => config('app.name'),
        ], [], [
            'title' => $title,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_left' => 10,
            'margin_right' => 10,
            'orientation' => 'L',
        ]);
    }
}
