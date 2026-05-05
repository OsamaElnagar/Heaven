<?php

namespace App\Filament\Resources\Packages\Actions;

use Filament\Actions\Action;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;

class ExportPackageSummaryAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'exportPackageSummary';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('تصدير ملخص الباقة')
            ->icon('heroicon-o-document-arrow-down')
            ->color('gray')
            ->action(function ($record) {
                $record->load('hotels', 'trips');
                $pdf = PDF::loadView('pdf.package-summary', [
                    'package' => $record,
                    'generatedAt' => now()->format('Y-m-d h:i A'),
                ]);

                return response()->streamDownload(
                    fn () => print ($pdf->output()),
                    'package-'.$record->id.'.pdf'
                );
            });
    }
}
