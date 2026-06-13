<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum CommissionType: string implements HasLabel
{
    case PERCENTAGE = 'percentage';
    case FIXED = 'fixed';

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::PERCENTAGE => 'نسبة مئوية',
            self::FIXED => 'مبلغ ثابت',
        };
    }
}
