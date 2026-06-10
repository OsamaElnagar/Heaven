<?php

namespace App\Filament\Resources\Rooms\Actions;

use App\Models\Booking;
use App\Models\Room;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;

class UnassignClientAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'unassignClient';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('إزالة حاج')
            ->icon('heroicon-o-user-minus')
            ->color('danger')
            ->schema([
                Select::make('booking_id')
                    ->label('الحجز')
                    ->options(function (Room $record) {
                        return Booking::where('room_id', $record->id)
                            ->with('client')
                            ->get()
                            ->mapWithKeys(fn ($b) => [$b->id => $b->reference.' - '.$b->client->name]);
                    })
                    ->required()
                    ->searchable()
                    ->native(false),
            ])
            ->requiresConfirmation()
            ->modalHeading('إزالة حاج من الغرفة')
            ->action(function (Room $record, array $data) {
                Booking::where('id', $data['booking_id'])->update(['room_id' => null]);

                if ($record->occupied > 0) {
                    $record->decrement('occupied');
                }

                Notification::make()->title('تمت الإزالة')->success()->send();
            });
    }
}
