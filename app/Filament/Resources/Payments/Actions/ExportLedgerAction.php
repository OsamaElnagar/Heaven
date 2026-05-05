<?php

namespace App\Filament\Resources\Payments\Actions;

use App\Models\Payment;
use Filament\Actions\Action;

class ExportLedgerAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'exportLedger';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('تصدير السجل')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('gray')
            ->action(function () {
                $payments = Payment::with('booking.client')
                    ->latest('paid_at')
                    ->get()
                    ->map(fn ($p) => [
                        'الحجز' => $p->booking?->reference,
                        'العميل' => $p->booking?->client?->name,
                        'النوع' => $p->type?->getLabel(),
                        'الطريقة' => $p->method?->getLabel(),
                        'المبلغ' => $p->amount,
                        'التاريخ' => $p->paid_at?->format('Y-m-d'),
                    ])
                    ->toArray();

                $filename = 'payments-ledger-'.now()->format('Y-m-d').'.csv';
                $headers = [
                    'Content-Type' => 'text/csv; charset=UTF-8',
                    'Content-Disposition' => 'attachment; filename="'.$filename.'"',
                ];
                $callback = function () use ($payments) {
                    $file = fopen('php://output', 'w');
                    fwrite($file, "\xEF\xBB\xBF"); // BOM for Arabic
                    if (! empty($payments)) {
                        fputcsv($file, array_keys($payments[0]));
                        foreach ($payments as $row) {
                            fputcsv($file, $row);
                        }
                    }
                    fclose($file);
                };

                return response()->stream($callback, 200, $headers);
            });
    }
}
