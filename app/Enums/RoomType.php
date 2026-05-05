<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum RoomType: string implements HasColor, HasLabel
{
    case SINGLE = 'single';
    case DOUBLE = 'double';
    case TRIPLE = 'triple';
    case QUAD = 'quad';
    case QUINT = 'quint';
    case SEXTUPLE = 'sextuple';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::SINGLE => 'warning',
            self::DOUBLE => 'info',
            self::TRIPLE => 'success',
            self::QUAD => 'gray',
            self::QUINT => 'gray',
            self::SEXTUPLE => 'gray',
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::SINGLE => 'فردي',
            self::DOUBLE => 'ثنائي',
            self::TRIPLE => 'ثلاثي',
            self::QUAD => 'رباعي',
            self::QUINT => 'خماسي',
            self::SEXTUPLE => 'سداسي',
        };
    }
}
