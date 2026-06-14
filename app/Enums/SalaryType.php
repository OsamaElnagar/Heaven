<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum SalaryType: string implements HasColor, HasLabel
{
    case MONTHLY = 'monthly';
    case DAILY = 'daily';
    case HOURLY = 'hourly';
    case PER_TRIP = 'per_trip';
    case COMMISSION = 'commission';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::MONTHLY => 'info',
            self::DAILY => 'warning',
            self::HOURLY => 'gray',
            self::PER_TRIP => 'success',
            self::COMMISSION => 'danger',
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::MONTHLY => 'شهري',
            self::DAILY => 'يومي',
            self::HOURLY => 'بالساعة',
            self::PER_TRIP => 'بالرحلة',
            self::COMMISSION => 'عمولة',
        };
    }
}
