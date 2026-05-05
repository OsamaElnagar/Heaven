<?php

namespace App\Filament\Resources\Visas\Actions;

use App\Models\Visa;
use Filament\Actions\Action;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;

class ExportVisaListAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'exportVisaList';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('تصدير قائمة التأشيرات')
            ->icon('heroicon-o-document-arrow-down')
            ->color('gray')
            ->action(function () {
                $visas = Visa::with('booking.client', 'booking.trip')
                    ->latest()
                    ->get();

                $pdf = PDF::loadView('pdf.visa-list', [
                    'visas' => $visas,
                    'generatedAt' => now()->format('Y-m-d h:i A'),
                ]);

                return response()->streamDownload(
                    fn () => print ($pdf->output()),
                    'visa-list-'.now()->format('Y-m-d').'.pdf'
                );
            });
    }
}
