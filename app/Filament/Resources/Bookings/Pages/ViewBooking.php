<?php

namespace App\Filament\Resources\Bookings\Pages;

use App\Filament\Resources\Bookings\Actions\AssignRoomAction;
use App\Filament\Resources\Bookings\Actions\CancelBookingAction;
use App\Filament\Resources\Bookings\Actions\ConfirmBookingAction;
use App\Filament\Resources\Bookings\Actions\IssueRefundAction;
use App\Filament\Resources\Bookings\Actions\PrintBookingVoucherAction;
use App\Filament\Resources\Bookings\BookingResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBooking extends ViewRecord
{
    protected static string $resource = BookingResource::class;

    protected static ?string $title = 'عرض حجز';

    protected function getHeaderActions(): array
    {
        return [
            ConfirmBookingAction::make(),
            CancelBookingAction::make(),
            IssueRefundAction::make(),
            AssignRoomAction::make(),
            PrintBookingVoucherAction::make(),
            EditAction::make()->label('تعديل'),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
