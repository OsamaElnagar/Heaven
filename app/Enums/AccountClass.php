<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum AccountClass: string implements HasColor, HasIcon, HasLabel
{
    case ASSETS = 'assets';
    case LIABILITIES = 'liabilities';
    case EQUITY = 'equity';
    case REVENUE = 'revenue';
    case EXPENSES = 'expenses';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::ASSETS => 'blue',
            self::LIABILITIES => 'red',
            self::EQUITY => 'purple',
            self::REVENUE => 'green',
            self::EXPENSES => 'orange',
        };
    }

    public function getIcon(): string|\BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::ASSETS => 'heroicon-o-building-library',
            self::LIABILITIES => 'heroicon-o-credit-card',
            self::EQUITY => 'heroicon-o-user-group',
            self::REVENUE => 'heroicon-o-arrow-trending-up',
            self::EXPENSES => 'heroicon-o-arrow-trending-down',
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::ASSETS => 'أصول',
            self::LIABILITIES => 'التزامات',
            self::EQUITY => 'حقوق الملكية',
            self::REVENUE => 'إيرادات',
            self::EXPENSES => 'مصروفات',
        };
    }
}
