<?php

namespace App\Filament\Resources\Trips\Actions;

use App\Models\Trip;
use Filament\Actions\Action;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;

class ExportRoomingListAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'exportRoomingList';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('تصدير قائمة الغرف')
            ->icon('heroicon-o-building-office')
            ->color('gray')
            ->action(function (Trip $record) {
                $rooms = $record->rooms()
                    ->with(['hotel', 'bookings.client'])
                    ->get()
                    ->groupBy(fn ($room) => $room->hotel->name);

                $pdf = PDF::loadView('pdf.trip-rooming', [
                    'trip' => $record,
                    'rooms' => $rooms,
                    'generatedAt' => now()->format('Y-m-d h:i A'),
                ]);

                return response()->streamDownload(
                    fn () => print ($pdf->output()),
                    'rooming-trip-'.$record->id.'.pdf'
                );
            });
    }
}
