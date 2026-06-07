<?php

namespace App\Filament\Resources\ReceiptVouchers\Pages;

use App\Enums\ExpenseStatus;
use App\Filament\Resources\ReceiptVouchers\Actions\PostVoucherAction;
use App\Filament\Resources\ReceiptVouchers\ReceiptVoucherResource;
use App\Models\ReceiptVoucher;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\ViewRecord;

class ViewReceiptVoucher extends ViewRecord
{
    protected static string $resource = ReceiptVoucherResource::class;

    protected static ?string $title = 'عرض سند قبض';

    protected function getHeaderActions(): array
    {
        return [
            PostVoucherAction::make(),
            EditAction::make(),
            DeleteAction::make()
                ->visible(fn (ReceiptVoucher $record): bool => $record->status !== ExpenseStatus::POSTED),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
