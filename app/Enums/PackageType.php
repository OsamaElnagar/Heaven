<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum PackageType: string implements HasColor, HasIcon, HasLabel
{
    case HAJJ = 'hajj';
    case UMRAH = 'umrah';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::HAJJ => 'warning',
            self::UMRAH => 'success',
        };
    }

    public function getIcon(): string|\BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::HAJJ => 'heroicon-o-star',
            self::UMRAH => 'heroicon-o-moon',
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::HAJJ => 'حج',
            self::UMRAH => 'عمرة',
        };
    }
}
