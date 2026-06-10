<?php

namespace App\Filament\Resources\BankAccounts\Pages;

use App\Filament\Resources\BankAccounts\BankAccountResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditBankAccount extends EditRecord
{
    protected static string $resource = BankAccountResource::class;

    protected static ?string $title = 'تعديل حساب بنكي';

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function ($record, DeleteAction $action): void {
                    if ($record->journalLines()->exists()) {
                        Notification::make()
                            ->title('لا يمكن الحذف')
                            ->body('لا يمكن حذف هذا الحساب البنكي لأنه مرتبطة بقيود يومية.')
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
                            ->body('لا يمكن حذف هذا الحساب البنكي لأنه مرتبطة بقيود يومية.')
                            ->danger()
                            ->send();

                        $action->cancelled();
                    }
                }),
            RestoreAction::make(),
        ];
    }
}
