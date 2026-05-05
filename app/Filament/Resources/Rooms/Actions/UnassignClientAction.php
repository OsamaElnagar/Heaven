<?php

namespace App\Filament\Resources\Rooms\Actions;

use Filament\Actions\Action;
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

        $this->label('إزالة')
            ->icon('heroicon-o-user-minus')
            ->color('danger')
            ->requiresConfirmation()
            ->action(function ($record) {
                $room = $record->room;
                $record->update(['room_id' => null]);
                if ($room && $room->occupied > 0) {
                    $room->decrement('occupied');
                }
                Notification::make()->title('تمت الإزالة')->success()->send();
            });
    }
}
