<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum CashFlowCategory: string implements HasColor, HasLabel
{
    case OPERATING = 'operating';
    case INVESTING = 'investing';
    case FINANCING = 'financing';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::OPERATING => 'info',
            self::INVESTING => 'success',
            self::FINANCING => 'warning',
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::OPERATING => 'تشغيلي',
            self::INVESTING => 'استثماري',
            self::FINANCING => 'تمويلي',
        };
    }
}
