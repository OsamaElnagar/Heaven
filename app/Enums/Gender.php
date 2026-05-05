<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum Gender: string implements HasColor, HasIcon, HasLabel
{
    case MALE = 'male';
    case FEMALE = 'female';
    case CHILD = 'child';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::MALE => 'info',
            self::FEMALE => 'warning',
            self::CHILD => 'success',
        };
    }

    public function getIcon(): string|\BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::MALE => 'heroicon-o-user',
            self::FEMALE => 'heroicon-o-user',
            self::CHILD => 'heroicon-o-user',
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::MALE => 'ذكر',
            self::FEMALE => 'أنثى',
            self::CHILD => 'طفل',
        };
    }
}
