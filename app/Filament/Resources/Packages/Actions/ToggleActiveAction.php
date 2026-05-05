<?php

namespace App\Filament\Resources\Packages\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ToggleActiveAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'toggleActive';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(fn ($record) => $record->is_active ? 'إلغاء التفعيل' : 'تفعيل')
            ->icon(fn ($record) => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
            ->color(fn ($record) => $record->is_active ? 'danger' : 'success')
            ->requiresConfirmation()
            ->action(function ($record) {
                $record->update(['is_active' => ! $record->is_active]);
                Notification::make()->title('تم تحديث الحالة')->success()->send();
            });
    }
}
