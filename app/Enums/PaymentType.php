<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum PaymentType: string implements HasColor, HasLabel
{
    case DEPOSIT = 'deposit';
    case INSTALLMENT = 'installment';
    case FINAL = 'final';
    case REFUND = 'refund';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::DEPOSIT => 'info',
            self::INSTALLMENT => 'warning',
            self::FINAL => 'success',
            self::REFUND => 'danger',
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::DEPOSIT => 'عربون',
            self::INSTALLMENT => 'قسط',
            self::FINAL => 'دفعة أخيرة',
            self::REFUND => 'استرداد',
        };
    }
}
