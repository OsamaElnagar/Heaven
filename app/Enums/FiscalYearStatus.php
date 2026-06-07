<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum FiscalYearStatus: string implements HasColor, HasIcon, HasLabel
{
    case OPEN = 'open';
    case CLOSED = 'closed';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::OPEN => 'success',
            self::CLOSED => 'danger',
        };
    }

    public function getIcon(): string|\BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::OPEN => 'heroicon-o-lock-open',
            self::CLOSED => 'heroicon-o-lock-closed',
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::OPEN => 'مفتوحة',
            self::CLOSED => 'مغلقة',
        };
    }
}
