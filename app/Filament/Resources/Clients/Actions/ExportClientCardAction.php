<?php

namespace App\Filament\Resources\Clients\Actions;

use App\Models\Client;
use Filament\Actions\Action;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;

class ExportClientCardAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'exportClientCard';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('بطاقة الحاج')
            ->icon('heroicon-o-identification')
            ->color('info')
            ->action(function (Client $record) {
                $pdf = PDF::loadView('pdf.client-card', [
                    'client' => $record->load('bookings.package', 'bookings.visa', 'bookings.trip'),
                ]);

                return response()->streamDownload(
                    fn () => print ($pdf->output()),
                    'client-card-'.$record->id.'.pdf'
                );
            });
    }
}
