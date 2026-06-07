<?php

namespace App\Filament\Resources\PaymentVouchers\Pages;

use App\Filament\Resources\PaymentVouchers\PaymentVoucherResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePaymentVoucher extends CreateRecord
{
    protected static string $resource = PaymentVoucherResource::class;

    protected static ?string $title = 'إنشاء سند صرف';
}
