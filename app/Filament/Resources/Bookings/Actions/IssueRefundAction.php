<?php

namespace App\Filament\Resources\Bookings\Actions;

use App\Enums\PaymentMethod;
use App\Models\Booking;
use App\Services\PaymentService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

class IssueRefundAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'issueRefund';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('استرداد')
            ->icon('heroicon-o-arrow-uturn-left')
            ->color('danger')
            ->form([
                TextInput::make('amount')
                    ->label('المبلغ')
                    ->required()
                    ->numeric()
                    ->prefix('ج.م'),
                Select::make('method')
                    ->label('طريقة الدفع')
                    ->options(PaymentMethod::class)
                    ->required()
                    ->native(false),
            ])
            ->modalHeading('استرداد مبلغ')
            ->action(function (Booking $record, array $data) {
                (new PaymentService)->issueRefund($record, $data['amount'], $data['method']);
                Notification::make()->title('تم تسجيل الاسترداد')->success()->send();
            });
    }
}
