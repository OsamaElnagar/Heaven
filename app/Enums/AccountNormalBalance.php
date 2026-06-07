<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum AccountNormalBalance: string implements HasColor, HasIcon, HasLabel
{
    case DEBIT = 'debit';
    case CREDIT = 'credit';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::DEBIT => 'info',
            self::CREDIT => 'warning',
        };
    }

    public function getIcon(): string|\BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::DEBIT => 'heroicon-o-arrow-down',
            self::CREDIT => 'heroicon-o-arrow-up',
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::DEBIT => 'مدين',
            self::CREDIT => 'دائن',
        };
    }
}
