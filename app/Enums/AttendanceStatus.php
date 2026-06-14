<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum AttendanceStatus: string implements HasColor, HasIcon, HasLabel
{
    case PRESENT = 'present';
    case ABSENT = 'absent';
    case LATE = 'late';
    case HALF_DAY = 'half_day';
    case OVERTIME = 'overtime';
    case WEEKEND = 'weekend';
    case HOLIDAY = 'holiday';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PRESENT => 'success',
            self::ABSENT => 'danger',
            self::LATE => 'warning',
            self::HALF_DAY => 'info',
            self::OVERTIME => 'success',
            self::WEEKEND => 'gray',
            self::HOLIDAY => 'gray',
        };
    }

    public function getIcon(): string|\BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::PRESENT => 'heroicon-o-check-circle',
            self::ABSENT => 'heroicon-o-x-circle',
            self::LATE => 'heroicon-o-clock',
            self::HALF_DAY => 'heroicon-o-sun',
            self::OVERTIME => 'heroicon-o-bolt',
            self::WEEKEND => 'heroicon-o-calendar',
            self::HOLIDAY => 'heroicon-o-star',
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::PRESENT => 'حاضر',
            self::ABSENT => 'غائب',
            self::LATE => 'متأخر',
            self::HALF_DAY => 'نصف يوم',
            self::OVERTIME => 'إضافي',
            self::WEEKEND => 'إجازة أسبوعية',
            self::HOLIDAY => 'إجازة رسمية',
        };
    }
}
