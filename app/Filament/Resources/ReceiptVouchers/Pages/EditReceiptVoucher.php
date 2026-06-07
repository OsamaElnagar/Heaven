<?php

namespace App\Filament\Resources\ReceiptVouchers\Pages;

use App\Enums\ExpenseStatus;
use App\Filament\Resources\ReceiptVouchers\Actions\PostVoucherAction;
use App\Filament\Resources\ReceiptVouchers\ReceiptVoucherResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditReceiptVoucher extends EditRecord
{
    protected static string $resource = ReceiptVoucherResource::class;

    protected static ?string $title = 'تعديل سند قبض';

    public function mount(int|string $record): void
    {
        parent::mount($record);

        if ($this->getRecord()->status === ExpenseStatus::POSTED) {
            Notification::make()
                ->title('لا يمكن تعديل سند قبض مرحّل')
                ->danger()
                ->send();

            $this->redirect($this->getResource()::getUrl('view', ['record' => $this->getRecord()]));
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            PostVoucherAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
