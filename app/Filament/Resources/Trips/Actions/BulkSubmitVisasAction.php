<?php

namespace App\Filament\Resources\Trips\Actions;

use App\Models\Trip;
use App\Services\VisaService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class BulkSubmitVisasAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'bulkSubmitVisas';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('تقديم التأشيرات')
            ->icon('heroicon-o-paper-airplane')
            ->color('info')
            ->requiresConfirmation()
            ->modalHeading('تقديم جميع التأشيرات')
            ->modalDescription('سيتم تقديم جميع التأشيرات غير المقدمة للحجوزات المؤكدة في هذه الرحلة.')
            ->action(function (Trip $record) {
                (new VisaService)->bulkSubmitForTrip($record);
                Notification::make()->title('تم تقديم التأشيرات بنجاح')->success()->send();
            });
    }
}
