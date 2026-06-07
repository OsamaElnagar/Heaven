<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum ExpenseStatus: string implements HasColor, HasIcon, HasLabel
{
    case DRAFT = 'draft';
    case POSTED = 'posted';
    case CANCELLED = 'cancelled';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::DRAFT => 'warning',
            self::POSTED => 'success',
            self::CANCELLED => 'danger',
        };
    }

    public function getIcon(): string|\BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::DRAFT => 'heroicon-o-pencil',
            self::POSTED => 'heroicon-o-check-circle',
            self::CANCELLED => 'heroicon-o-x-circle',
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::DRAFT => 'مسودة',
            self::POSTED => 'مرحّل',
            self::CANCELLED => 'ملغي',
        };
    }
}
