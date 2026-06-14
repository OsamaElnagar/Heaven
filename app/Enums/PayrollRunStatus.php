<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum PayrollRunStatus: string implements HasColor, HasIcon, HasLabel
{
    case DRAFT = 'draft';
    case APPROVED = 'approved';
    case POSTED = 'posted';
    case PAID = 'paid';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::APPROVED => 'info',
            self::POSTED => 'success',
            self::PAID => 'primary',
        };
    }

    public function getIcon(): string|\BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::DRAFT => 'heroicon-o-pencil',
            self::APPROVED => 'heroicon-o-check-badge',
            self::POSTED => 'heroicon-o-check-circle',
            self::PAID => 'heroicon-o-banknotes',
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::DRAFT => 'مسودة',
            self::APPROVED => 'معتمد',
            self::POSTED => 'مرحل',
            self::PAID => 'مدفوع',
        };
    }
}
