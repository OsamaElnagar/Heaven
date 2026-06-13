<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum BookingChannel: string implements HasColor, HasIcon, HasLabel
{
    case DIRECT = 'direct';
    case BRANCH = 'branch';
    case AGENT = 'agent';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::DIRECT => 'info',
            self::BRANCH => 'success',
            self::AGENT => 'warning',
        };
    }

    public function getIcon(): string|\BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::DIRECT => 'heroicon-o-building-office',
            self::BRANCH => 'heroicon-o-building-storefront',
            self::AGENT => 'heroicon-o-user',
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::DIRECT => 'مباشر',
            self::BRANCH => 'فرع',
            self::AGENT => 'وكيل',
        };
    }
}
