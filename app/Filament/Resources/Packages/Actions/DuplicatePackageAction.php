<?php

namespace App\Filament\Resources\Packages\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;

class DuplicatePackageAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'duplicatePackage';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('نسخ الباقة')
            ->icon('heroicon-o-document-duplicate')
            ->color('warning')
            ->requiresConfirmation()
            ->action(function ($record) {
                $new = $record->replicate();
                $new->reserved_seats = 0;
                $new->is_active = true;
                $new->save();
                Notification::make()->title('تم نسخ الباقة بنجاح')->success()->send();
            });
    }
}
