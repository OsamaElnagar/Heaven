<?php

namespace App\Filament\Resources\Safes\Pages;

use App\Filament\Resources\Safes\SafeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditSafe extends EditRecord
{
    protected static string $resource = SafeResource::class;

    protected static ?string $title = 'تعديل خزينة';

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function ($record, DeleteAction $action): void {
                    if ($record->journalLines()->exists()) {
                        Notification::make()
                            ->title('لا يمكن الحذف')
                            ->body('لا يمكن حذف هذه الخزينة لأنها مرتبطة بقيود يومية.')
                            ->danger()
                            ->send();

                        $action->cancelled();
                    }
                }),
            ForceDeleteAction::make()
                ->before(function ($record, ForceDeleteAction $action): void {
                    if ($record->journalLines()->exists()) {
                        Notification::make()
                            ->title('لا يمكن الحذف')
                            ->body('لا يمكن حذف هذه الخزينة لأنها مرتبطة بقيود يومية.')
                            ->danger()
                            ->send();

                        $action->cancelled();
                    }
                }),
            RestoreAction::make(),
        ];
    }
}
