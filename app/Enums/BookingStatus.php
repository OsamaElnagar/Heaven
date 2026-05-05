<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum BookingStatus: string implements HasColor, HasIcon, HasLabel
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case CANCELLED = 'cancelled';
    case COMPLETED = 'completed';
    case REFUNDED = 'refunded';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::CONFIRMED => 'success',
            self::CANCELLED => 'danger',
            self::COMPLETED => 'info',
            self::REFUNDED => 'gray',
        };
    }

    public function getIcon(): string|\BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::PENDING => 'heroicon-o-clock',
            self::CONFIRMED => 'heroicon-o-check-circle',
            self::CANCELLED => 'heroicon-o-x-circle',
            self::COMPLETED => 'heroicon-o-flag',
            self::REFUNDED => 'heroicon-o-arrow-uturn-left',
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::PENDING => 'معلق',
            self::CONFIRMED => 'مؤكد',
            self::CANCELLED => 'ملغي',
            self::COMPLETED => 'مكتمل',
            self::REFUNDED => 'مسترد',
        };
    }
}
