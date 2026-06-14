<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum PayrollRunType: string implements HasColor, HasIcon, HasLabel
{
    case MONTHLY = 'monthly';
    case DAILY = 'daily';
    case BONUS = 'bonus';
    case DEDUCTION = 'deduction';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::MONTHLY => 'success',
            self::DAILY => 'info',
            self::BONUS => 'warning',
            self::DEDUCTION => 'danger',
        };
    }

    public function getIcon(): string|\BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::MONTHLY => 'heroicon-o-calendar',
            self::DAILY => 'heroicon-o-calendar-days',
            self::BONUS => 'heroicon-o-star',
            self::DEDUCTION => 'heroicon-o-arrow-trending-down',
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::MONTHLY => 'شهري',
            self::DAILY => 'يومي',
            self::BONUS => 'مكافأة',
            self::DEDUCTION => 'خصم',
        };
    }
}
