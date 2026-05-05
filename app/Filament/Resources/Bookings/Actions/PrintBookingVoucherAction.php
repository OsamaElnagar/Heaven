<?php

namespace App\Filament\Resources\Bookings\Actions;

use App\Models\Booking;
use Filament\Actions\Action;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;

class PrintBookingVoucherAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'printBookingVoucher';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('قسيمة الحجز')
            ->icon('heroicon-o-ticket')
            ->color('gray')
            ->action(function (Booking $record) {
                $record->load('client', 'package', 'trip', 'visa', 'room');
                $pdf = PDF::loadView('pdf.booking-voucher', [
                    'booking' => $record,
                    'generatedAt' => now()->format('Y-m-d h:i A'),
                ]);

                return response()->streamDownload(
                    fn () => print ($pdf->output()),
                    'voucher-'.$record->reference.'.pdf'
                );
            });
    }
}
