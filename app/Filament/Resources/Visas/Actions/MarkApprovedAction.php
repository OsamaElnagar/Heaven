<?php

namespace App\Filament\Resources\Visas\Actions;

use App\Enums\VisaStatus;
use App\Models\Visa;
use App\Services\VisaService;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

class MarkApprovedAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'markApproved';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('موافقة')
            ->icon('heroicon-o-check-badge')
            ->color('success')
            ->schema([
                TextInput::make('visa_number')
                    ->label('رقم التأشيرة')
                    ->required(),
                DatePicker::make('expiry_date')
                    ->label('تاريخ الانتهاء')
                    ->required()
                    ->native(false),
            ])
            ->modalHeading('تسجيل الموافقة على التأشيرة')
            ->visible(fn (Visa $record): bool => $record->status->canTransitionTo(VisaStatus::APPROVED))
            ->action(function (Visa $record, array $data) {
                $visaService = app(VisaService::class);
                $visaService->markApproved(
                    $record,
                    $data['visa_number'],
                    Carbon::parse($data['expiry_date']),
                );
                Notification::make()->title('تم تسجيل الموافقة')->success()->send();
            });
    }
}
