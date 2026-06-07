<?php

namespace App\Filament\Resources\Accounts\Actions;

use App\Models\Account;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class DeactivateAccountAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'deactivateAccount';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('إيقاف')
            ->icon('heroicon-o-x-circle')
            ->color('warning')
            ->visible(fn (Account $record): bool => $record->is_active && ! $record->is_system)
            ->action(function (Account $record): void {
                $record->update(['is_active' => false]);

                Notification::make()
                    ->title('تم إيقاف الحساب')
                    ->success()
                    ->send();
            });
    }
}
