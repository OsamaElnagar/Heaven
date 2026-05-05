<?php

namespace App\Filament\Resources\Payments\Actions;

use App\Models\Payment as PaymentModel;
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
            ->action(function (PaymentModel $record) {
                $record->load('booking.client', 'booking.package');
                $pdf = PDF::loadView('pdf.payment-receipt', [
                    'payment' => $record,
                    'generatedAt' => now()->format('Y-m-d h:i A'),
                ]);

                return response()->streamDownload(
                    fn () => print ($pdf->output()),
                    'receipt-'.$record->id.'.pdf'
                );
            });
    }
}
