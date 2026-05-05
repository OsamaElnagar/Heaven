<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum TripStatus: string implements HasColor, HasIcon, HasLabel
{
    case UPCOMING = 'upcoming';
    case DEPARTED = 'departed';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::UPCOMING => 'info',
            self::DEPARTED => 'warning',
            self::COMPLETED => 'success',
            self::CANCELLED => 'danger',
        };
    }

    public function getIcon(): string|\BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::UPCOMING => 'heroicon-o-clock',
            self::DEPARTED => 'heroicon-o-paper-airplane',
            self::COMPLETED => 'heroicon-o-flag',
            self::CANCELLED => 'heroicon-o-x-circle',
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::UPCOMING => 'قادمة',
            self::DEPARTED => 'مغادرة',
            self::COMPLETED => 'مكتملة',
            self::CANCELLED => 'ملغاة',
        };
    }
}
