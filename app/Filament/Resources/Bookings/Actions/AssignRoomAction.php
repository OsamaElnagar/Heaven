<?php

namespace App\Filament\Resources\Bookings\Actions;

use App\Models\Booking;
use App\Models\Room;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;

class AssignRoomAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'assignRoom';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('تخصيص غرفة')
            ->icon('heroicon-o-home-modern')
            ->color('info')
            ->form([
                Select::make('room_id')
                    ->label('الغرفة')
                    ->options(function (Booking $record) {
                        if (! $record->trip_id) {
                            return [];
                        }

                        return Room::where('trip_id', $record->trip_id)
                            ->whereColumn('occupied', '<', 'capacity')
                            ->with('hotel')
                            ->get()
                            ->mapWithKeys(fn ($room) => [
                                $room->id => ($room->hotel->name ?? '').' - '.($room->room_number ?? 'غرفة '.$room->id).' ('.$room->occupied.'/'.$room->capacity.')',
                            ]);
                    })
                    ->required()
                    ->searchable()
                    ->native(false),
            ])
            ->modalHeading('تخصيص غرفة')
            ->action(function (Booking $record, array $data) {
                $room = Room::find($data['room_id']);
                if ($room->occupied >= $room->capacity) {
                    Notification::make()->title('الغرفة ممتلئة')->danger()->send();

                    return;
                }
                $record->update(['room_id' => $room->id]);
                $room->increment('occupied');
                Notification::make()->title('تم تخصيص الغرفة')->success()->send();
            });
    }
}
