<?php

namespace App\Filament\Resources\RefundVouchers\Pages;

use App\Enums\ExpenseStatus;
use App\Filament\Resources\RefundVouchers\Actions\PostVoucherAction;
use App\Filament\Resources\RefundVouchers\RefundVoucherResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditRefundVoucher extends EditRecord
{
    protected static string $resource = RefundVoucherResource::class;

    protected static ?string $title = 'تعديل سند استرداد';

    public function mount(int|string $record): void
    {
        parent::mount($record);

        if ($this->getRecord()->status === ExpenseStatus::POSTED) {
            Notification::make()
                ->title('لا يمكن تعديل سند استرداد مرحّل')
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
