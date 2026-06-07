<?php

namespace App\Filament\Resources\Visas\Actions;

use App\Enums\VisaStatus;
use App\Models\Visa;
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
            ->visible(fn (Visa $record) => ! in_array($record->status, [VisaStatus::APPROVED, VisaStatus::REJECTED], true))
            ->action(function (Visa $record, array $data) {
                $record->update([
                    'status' => VisaStatus::REJECTED,
                    'rejection_reason' => $data['reason'],
                ]);
                Notification::make()->title('تم تسجيل الرفض')->success()->send();
            });
    }
}
