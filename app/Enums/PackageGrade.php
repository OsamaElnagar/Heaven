<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum PackageGrade: string implements HasColor, HasLabel
{
    case ECONOMY = 'economy';
    case STANDARD = 'standard';
    case VIP = 'vip';
    case VVIP = 'vvip';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::ECONOMY => 'gray',
            self::STANDARD => 'info',
            self::VIP => 'warning',
            self::VVIP => 'danger',
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::ECONOMY => 'اقتصادي',
            self::STANDARD => 'عادي',
            self::VIP => 'VIP',
            self::VVIP => 'VVIP',
        };
    }
}
