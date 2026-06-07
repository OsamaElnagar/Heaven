<?php

namespace App\Filament\Resources\RefundVouchers\Pages;

use App\Filament\Resources\RefundVouchers\RefundVoucherResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRefundVoucher extends CreateRecord
{
    protected static string $resource = RefundVoucherResource::class;

    protected static ?string $title = 'إنشاء سند استرداد';
}
