<?php

namespace App\Filament\Resources\Bookings\Actions;

use App\Models\Booking;
use Filament\Actions\Action;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;

class PrintReceiptAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'printReceipt';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('إيصال')
            ->icon('heroicon-o-receipt-percent')
            ->color('gray')
            ->action(function (Booking $record) {
                $record->load('client', 'payments');
                $pdf = PDF::loadView('pdf.booking-receipt', [
                    'booking' => $record,
                    'generatedAt' => now()->format('Y-m-d h:i A'),
                ]);

                return response()->streamDownload(
                    fn () => print ($pdf->output()),
                    'receipt-'.$record->reference.'.pdf'
                );
            });
    }
}
