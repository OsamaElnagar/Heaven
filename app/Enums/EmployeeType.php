<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum EmployeeType: string implements HasColor, HasIcon, HasLabel
{
    case PERMANENT = 'permanent';
    case TEMPORARY = 'temporary';
    case DAILY = 'daily';
    case CONTRACTED = 'contracted';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PERMANENT => 'success',
            self::TEMPORARY => 'warning',
            self::DAILY => 'info',
            self::CONTRACTED => 'gray',
        };
    }

    public function getIcon(): string|\BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::PERMANENT => 'heroicon-o-user',
            self::TEMPORARY => 'heroicon-o-user-minus',
            self::DAILY => 'heroicon-o-calendar-days',
            self::CONTRACTED => 'heroicon-o-document-text',
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::PERMANENT => 'دائم',
            self::TEMPORARY => 'مؤقت',
            self::DAILY => 'يومية',
            self::CONTRACTED => 'بعقد',
        };
    }
}
