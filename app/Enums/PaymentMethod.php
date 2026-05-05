<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum PaymentMethod: string implements HasColor, HasIcon, HasLabel
{
    case CASH = 'cash';
    case BANK_TRANSFER = 'bank_transfer';
    case INSTAPAY = 'instapay';
    case CHECK = 'check';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::CASH => 'success',
            self::BANK_TRANSFER => 'info',
            self::INSTAPAY => 'warning',
            self::CHECK => 'gray',
        };
    }

    public function getIcon(): string|\BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::CASH => 'heroicon-o-banknotes',
            self::BANK_TRANSFER => 'heroicon-o-building-library',
            self::INSTAPAY => 'heroicon-o-device-phone-mobile',
            self::CHECK => 'heroicon-o-document-text',
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::CASH => 'كاش',
            self::BANK_TRANSFER => 'تحويل بنكي',
            self::INSTAPAY => 'انستاباي',
            self::CHECK => 'شيك',
        };
    }
}
