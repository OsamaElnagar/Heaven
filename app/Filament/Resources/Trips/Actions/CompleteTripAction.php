<?php

namespace App\Filament\Resources\Trips\Actions;

use App\Enums\TripStatus;
use App\Models\Trip;
use App\Services\TripService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class CompleteTripAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'completeTrip';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('إكمال الرحلة')
            ->icon('heroicon-o-flag')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('تأكيد إكمال الرحلة')
            ->modalDescription('سيتم تغيير حالة الرحلة وجميع حجوزاتها إلى "مكتمل".')
            ->visible(fn (Trip $record) => $record->status !== TripStatus::COMPLETED)
            ->action(function (Trip $record) {
                (new TripService)->complete($record);
                Notification::make()->title('تم إكمال الرحلة بنجاح')->success()->send();
            });
    }
}
