<?php

namespace App\Filament\Resources\Visas\Actions;

use App\Enums\VisaStatus;
use App\Services\VisaService;
use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;

class BulkSubmitAction extends BulkAction
{
    public static function getDefaultName(): ?string
    {
        return 'bulkSubmit';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('تقديم جماعي')
            ->icon('heroicon-o-paper-airplane')
            ->color('info')
            ->requiresConfirmation()
            ->modalHeading('تقديم التأشيرات')
            ->modalDescription('سيتم تحديث حالة التأشيرات المحددة إلى "تم التقديم".')
            ->action(function ($records) {
                $visaService = app(VisaService::class);
                $count = 0;
                foreach ($records as $visa) {
                    if ($visa->status->canTransitionTo(VisaStatus::APPLIED)) {
                        $visaService->submitApplication($visa);
                        $count++;
                    }
                }
                Notification::make()->title("تم تقديم {$count} تأشيرة")->success()->send();
            });
    }
}
