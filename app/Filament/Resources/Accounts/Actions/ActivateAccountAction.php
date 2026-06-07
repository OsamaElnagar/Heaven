<?php

namespace App\Filament\Resources\Accounts\Actions;

use App\Models\Account;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ActivateAccountAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'activateAccount';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('تنشيط')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->visible(fn (Account $record): bool => ! $record->is_active)
            ->action(function (Account $record): void {
                $record->update(['is_active' => true]);

                Notification::make()
                    ->title('تم تنشيط الحساب بنجاح')
                    ->success()
                    ->send();
            });
    }
}
