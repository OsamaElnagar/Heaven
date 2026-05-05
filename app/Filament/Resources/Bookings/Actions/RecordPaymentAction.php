<?php

namespace App\Filament\Resources\Bookings\Actions;

use App\Enums\PaymentMethod;
use App\Enums\PaymentType;
use App\Models\Booking;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

class RecordPaymentAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'recordPayment';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('تسجيل دفعة')
            ->icon('heroicon-o-currency-dollar')
            ->color('success')
            ->form([
                TextInput::make('amount')
                    ->label('المبلغ')
                    ->required()
                    ->numeric()
                    ->prefix('ج.م'),
                Select::make('type')
                    ->label('النوع')
                    ->options(PaymentType::class)
                    ->required()
                    ->native(false),
                Select::make('method')
                    ->label('طريقة الدفع')
                    ->options(PaymentMethod::class)
                    ->required()
                    ->native(false),
                DatePicker::make('paid_at')
                    ->label('تاريخ الدفع')
                    ->required()
                    ->default(now())
                    ->native(false),
            ])
            ->modalHeading('تسجيل دفعة')
            ->slideOver()
            ->action(function (Booking $record, array $data) {
                $record->payments()->create($data);
                Notification::make()->title('تم تسجيل الدفعة')->success()->send();
            });
    }
}
