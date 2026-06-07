<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum JournalEntryStatus: string implements HasColor, HasIcon, HasLabel
{
    case DRAFT = 'draft';
    case POSTED = 'posted';
    case REVERSED = 'reversed';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::DRAFT => 'warning',
            self::POSTED => 'success',
            self::REVERSED => 'danger',
        };
    }

    public function getIcon(): string|\BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::DRAFT => 'heroicon-o-pencil',
            self::POSTED => 'heroicon-o-check-circle',
            self::REVERSED => 'heroicon-o-arrow-uturn-left',
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::DRAFT => 'مسودة',
            self::POSTED => 'مرحّل',
            self::REVERSED => 'معكوس',
        };
    }
}
