<?php

namespace App\Filament\Resources\Bookings\Actions;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ConfirmBookingAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'confirmBooking';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('تأكيد الحجز')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('تأكيد الحجز')
            ->modalDescription('سيتم تغيير حالة الحجز إلى "مؤكد" وحجز مقعد في الباقة.')
            ->visible(fn (Booking $record) => $record->status === BookingStatus::PENDING)
            ->action(function (Booking $record) {
                if (($record->package->total_seats - $record->package->reserved_seats) <= 0) {
                    Notification::make()->title('لا توجد مقاعد متاحة في هذه الباقة')->danger()->send();

                    return;
                }
                $record->update(['status' => BookingStatus::CONFIRMED]);
                Notification::make()->title('تم تأكيد الحجز')->success()->send();
            });
    }
}
