<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum VoucherPaymentMethod: string implements HasColor, HasIcon, HasLabel
{
    case SAFE = 'safe';
    case BANK = 'bank';
    case CHEQUE = 'cheque';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::SAFE => 'success',
            self::BANK => 'info',
            self::CHEQUE => 'warning',
        };
    }

    public function getIcon(): string|\BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::SAFE => 'heroicon-o-currency-dollar',
            self::BANK => 'heroicon-o-banknotes',
            self::CHEQUE => 'heroicon-o-document-text',
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::SAFE => 'خزينة',
            self::BANK => 'تحويل بنكي',
            self::CHEQUE => 'شيك',
        };
    }
}
