<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum EmployeeAdvanceType: string implements HasColor, HasIcon, HasLabel
{
    case SHORT_TERM = 'short_term';
    case LONG_TERM = 'long_term';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::SHORT_TERM => 'warning',
            self::LONG_TERM => 'info',
        };
    }

    public function getIcon(): string|\BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::SHORT_TERM => 'heroicon-o-arrow-trending-up',
            self::LONG_TERM => 'heroicon-o-arrow-path',
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::SHORT_TERM => 'قصيرة الأجل',
            self::LONG_TERM => 'طويلة الأجل',
        };
    }
}
