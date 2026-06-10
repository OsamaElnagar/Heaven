<?php

namespace App\Filament\Resources\Rooms\Actions;

use App\Models\Booking;
use App\Models\Room;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class AssignClientAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'assignClient';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('إضافة حاج')
            ->icon('heroicon-o-user-plus')
            ->color('success')
            ->schema([
                Select::make('booking_id')
                    ->label('الحجز')
                    ->options(function (Room $record) {
                        return Booking::where('trip_id', $record->trip_id)
                            ->whereNull('room_id')
                            ->with('client')
                            ->get()
                            ->mapWithKeys(fn ($b) => [$b->id => $b->reference.' - '.$b->client->name]);
                    })
                    ->required()
                    ->searchable()
                    ->native(false),
            ])
            ->modalHeading('إضافة حاج للغرفة')
            ->action(function (Room $record, array $data) {
                $updated = DB::transaction(function () use ($record, $data) {
                    $room = Room::lockForUpdate()->find($record->id);

                    if ($room->occupied >= $room->capacity) {
                        return false;
                    }

                    Booking::where('id', $data['booking_id'])->update(['room_id' => $room->id]);
                    $room->increment('occupied');

                    return true;
                });

                if ($updated) {
                    Notification::make()->title('تمت الإضافة')->success()->send();
                } else {
                    Notification::make()->title('الغرفة ممتلئة')->danger()->send();
                }
            });
    }
}
