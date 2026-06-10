<?php

namespace App\Filament\Resources\Trips\Actions;

use App\Models\Trip;
use App\Services\TripService;
use Filament\Actions\Action;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;

class ExportManifestAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'exportManifest';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('تصدير كشف المسافرين')
            ->icon('heroicon-o-document-arrow-down')
            ->color('gray')
            ->action(function (Trip $record) {
                $bookings = (new TripService)->getManifest($record);

                $pdf = PDF::loadView('pdf.trip-manifest', [
                    'trip' => $record,
                    'bookings' => $bookings,
                    'generatedAt' => now()->format('Y-m-d h:i A'),
                ]);

                return response()->streamDownload(
                    fn () => print ($pdf->output()),
                    'manifest-trip-'.$record->id.'.pdf'
                );
            });
    }
}
