<?php

namespace App\Filament\Resources\RefundVouchers\Pages;

use App\Enums\ExpenseStatus;
use App\Filament\Resources\RefundVouchers\Actions\PostVoucherAction;
use App\Filament\Resources\RefundVouchers\RefundVoucherResource;
use App\Models\RefundVoucher;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\ViewRecord;

class ViewRefundVoucher extends ViewRecord
{
    protected static string $resource = RefundVoucherResource::class;

    protected static ?string $title = 'عرض سند استرداد';

    protected function getHeaderActions(): array
    {
        return [
            PostVoucherAction::make(),
            EditAction::make(),
            DeleteAction::make()
                ->visible(fn (RefundVoucher $record): bool => $record->status !== ExpenseStatus::POSTED),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
