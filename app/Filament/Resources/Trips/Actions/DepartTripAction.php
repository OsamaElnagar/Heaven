<?php

namespace App\Filament\Resources\Trips\Actions;

use App\Enums\TripStatus;
use App\Models\Trip;
use App\Services\TripService;
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
                try {
                    (new TripService)->depart($record);
                    Notification::make()->title('تم تسجيل مغادرة الرحلة')->success()->send();
                } catch (\InvalidArgumentException $e) {
                    Notification::make()->title($e->getMessage())->danger()->send();
                }
            });
    }
}
