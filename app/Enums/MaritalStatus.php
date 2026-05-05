<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum MaritalStatus: string implements HasLabel
{
    case SINGLE = 'single';
    case MARRIED = 'married';
    case WIDOWED = 'widowed';
    case DIVORCED = 'divorced';

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::SINGLE => 'أعزب',
            self::MARRIED => 'متزوج',
            self::WIDOWED => 'أرمل',
            self::DIVORCED => 'مطلق',
        };
    }
}
