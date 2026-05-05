<?php

namespace App\Filament\Resources\Visas\Actions;

use App\Enums\VisaStatus;
use App\Models\Visa;
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
            ->form([
                TextInput::make('visa_number')
                    ->label('رقم التأشيرة')
                    ->required(),
                DatePicker::make('expiry_date')
                    ->label('تاريخ الانتهاء')
                    ->required()
                    ->native(false),
            ])
            ->modalHeading('تسجيل الموافقة على التأشيرة')
            ->visible(fn (Visa $record) => $record->status !== VisaStatus::APPROVED)
            ->action(function (Visa $record, array $data) {
                $record->update([
                    'status' => VisaStatus::APPROVED,
                    'visa_number' => $data['visa_number'],
                    'expiry_date' => Carbon::parse($data['expiry_date']),
                ]);
                Notification::make()->title('تم تسجيل الموافقة')->success()->send();
            });
    }
}
