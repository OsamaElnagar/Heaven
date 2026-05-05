<?php

namespace App\Filament\Resources\Trips\Actions;

use App\Enums\BookingStatus;
use App\Enums\TripStatus;
use App\Models\Trip;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class DepartTripAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'departTrip';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('مغادرة الرحلة')
            ->icon('heroicon-o-paper-airplane')
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading('تأكيد المغادرة')
            ->modalDescription('سيتم تغيير حالة الرحلة إلى "مغادرة". تأكد من أن جميع الحجوزات مؤكدة.')
            ->visible(fn (Trip $record) => $record->status !== TripStatus::DEPARTED && $record->status !== TripStatus::COMPLETED)
            ->action(function (Trip $record) {
                $unconfirmed = $record->bookings()
                    ->whereNot('status', BookingStatus::CONFIRMED)
                    ->exists();

                if ($unconfirmed) {
                    Notification::make()->title('يجب تأكيد جميع الحجوزات أولاً')->danger()->send();

                    return;
                }

                $record->update(['status' => TripStatus::DEPARTED]);
                Notification::make()->title('تم تسجيل مغادرة الرحلة')->success()->send();
            });
    }
}
