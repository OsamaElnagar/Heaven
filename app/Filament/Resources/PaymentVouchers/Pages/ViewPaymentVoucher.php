<?php

namespace App\Filament\Resources\PaymentVouchers\Pages;

use App\Enums\ExpenseStatus;
use App\Filament\Resources\PaymentVouchers\Actions\PostVoucherAction;
use App\Filament\Resources\PaymentVouchers\PaymentVoucherResource;
use App\Models\PaymentVoucher;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPaymentVoucher extends ViewRecord
{
    protected static string $resource = PaymentVoucherResource::class;

    protected static ?string $title = 'عرض سند صرف';

    protected function getHeaderActions(): array
    {
        return [
            PostVoucherAction::make(),
            EditAction::make(),
            DeleteAction::make()
                ->visible(fn (PaymentVoucher $record): bool => $record->status !== ExpenseStatus::POSTED),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
