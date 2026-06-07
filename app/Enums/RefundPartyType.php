<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum RefundPartyType: string implements HasColor, HasIcon, HasLabel
{
    case CLIENT = 'client';
    case SUPPLIER = 'supplier';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::CLIENT => 'info',
            self::SUPPLIER => 'warning',
        };
    }

    public function getIcon(): string|\BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::CLIENT => 'heroicon-o-user-group',
            self::SUPPLIER => 'heroicon-o-shopping-cart',
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::CLIENT => 'استرداد للعميل',
            self::SUPPLIER => 'استرداد من المورد',
        };
    }
}
