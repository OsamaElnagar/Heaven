<?php

namespace App\Filament\Resources\ReceiptVouchers\Pages;

use App\Filament\Resources\ReceiptVouchers\ReceiptVoucherResource;
use Filament\Resources\Pages\CreateRecord;

class CreateReceiptVoucher extends CreateRecord
{
    protected static string $resource = ReceiptVoucherResource::class;

    protected static ?string $title = 'إنشاء سند قبض';
}
