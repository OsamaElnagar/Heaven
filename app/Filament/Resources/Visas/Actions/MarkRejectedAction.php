<?php

namespace App\Filament\Resources\Visas\Actions;

use App\Enums\VisaStatus;
use App\Models\Visa;
use App\Services\VisaService;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;

class MarkRejectedAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'markRejected';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('رفض')
            ->icon('heroicon-o-x-mark')
            ->color('danger')
            ->schema([
                Textarea::make('reason')
                    ->label('سبب الرفض')
                    ->required(),
            ])
            ->modalHeading('تسجيل رفض التأشيرة')
            ->visible(fn (Visa $record): bool => $record->status->canTransitionTo(VisaStatus::REJECTED))
            ->action(function (Visa $record, array $data) {
                $visaService = app(VisaService::class);
                $visaService->markRejected($record, $data['reason']);
                Notification::make()->title('تم تسجيل الرفض')->success()->send();
            });
    }
}
