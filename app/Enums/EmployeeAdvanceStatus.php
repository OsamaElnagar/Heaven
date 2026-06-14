<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum EmployeeAdvanceStatus: string implements HasColor, HasIcon, HasLabel
{
    case ACTIVE = 'active';
    case FULLY_REPAID = 'fully_repaid';
    case CANCELLED = 'cancelled';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::ACTIVE => 'warning',
            self::FULLY_REPAID => 'success',
            self::CANCELLED => 'danger',
        };
    }

    public function getIcon(): string|\BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::ACTIVE => 'heroicon-o-clock',
            self::FULLY_REPAID => 'heroicon-o-check-circle',
            self::CANCELLED => 'heroicon-o-x-circle',
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::ACTIVE => 'نشط',
            self::FULLY_REPAID => 'مسدد بالكامل',
            self::CANCELLED => 'ملغي',
        };
    }
}
