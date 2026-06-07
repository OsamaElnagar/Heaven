<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum JournalEntrySourceType: string implements HasColor, HasIcon, HasLabel
{
    case MANUAL = 'manual';
    case PAYMENT_VOUCHER = 'payment_voucher';
    case RECEIPT_VOUCHER = 'receipt_voucher';
    case REFUND_VOUCHER = 'refund_voucher';
    case OPENING_BALANCE = 'opening_balance';
    case REVERSAL = 'reversal';
    case EXPENSE = 'expense';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::MANUAL => 'info',
            self::PAYMENT_VOUCHER => 'warning',
            self::RECEIPT_VOUCHER => 'success',
            self::REFUND_VOUCHER => 'danger',
            self::OPENING_BALANCE => 'teal',
            self::REVERSAL => 'danger',
            self::EXPENSE => 'warning',
        };
    }

    public function getIcon(): string|\BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::MANUAL => 'heroicon-o-pencil',
            self::PAYMENT_VOUCHER => 'heroicon-o-arrow-up-circle',
            self::RECEIPT_VOUCHER => 'heroicon-o-arrow-down-circle',
            self::REFUND_VOUCHER => 'heroicon-o-arrow-uturn-left',
            self::OPENING_BALANCE => 'heroicon-o-archive-box',
            self::REVERSAL => 'heroicon-o-arrow-uturn-left',
            self::EXPENSE => 'heroicon-o-banknotes',
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::MANUAL => 'قيد يدوي',
            self::PAYMENT_VOUCHER => 'سند صرف',
            self::RECEIPT_VOUCHER => 'سند قبض',
            self::REFUND_VOUCHER => 'سند استرداد',
            self::OPENING_BALANCE => 'أرصدة أول المدة',
            self::REVERSAL => 'قيد عكسي',
            self::EXPENSE => 'مصروف',
        };
    }
}
