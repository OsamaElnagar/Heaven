<?php

namespace App\Filament\Resources\Bookings\Actions;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;

class CancelBookingAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'cancelBooking';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('إلغاء الحجز')
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->form([
                Textarea::make('reason')->label('سبب الإلغاء')->required(),
            ])
            ->modalHeading('إلغاء الحجز')
            ->visible(fn (Booking $record) => $record->status !== BookingStatus::CANCELLED && $record->status !== BookingStatus::REFUNDED)
            ->action(function (Booking $record, array $data) {
                $record->update([
                    'status' => BookingStatus::CANCELLED,
                    'notes' => $record->notes."\nسبب الإلغاء: ".$data['reason'],
                ]);
                Notification::make()->title('تم إلغاء الحجز')->success()->send();
            });
    }
}
