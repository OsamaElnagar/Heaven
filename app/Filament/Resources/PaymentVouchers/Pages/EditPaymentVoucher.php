<?php

namespace App\Filament\Resources\PaymentVouchers\Pages;

use App\Enums\ExpenseStatus;
use App\Filament\Resources\PaymentVouchers\Actions\PostVoucherAction;
use App\Filament\Resources\PaymentVouchers\PaymentVoucherResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPaymentVoucher extends EditRecord
{
    protected static string $resource = PaymentVoucherResource::class;

    protected static ?string $title = 'تعديل سند صرف';

    public function mount(int|string $record): void
    {
        parent::mount($record);

        if ($this->getRecord()->status === ExpenseStatus::POSTED) {
            Notification::make()
                ->title('لا يمكن تعديل سند صرف مرحّل')
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
