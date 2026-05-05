<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum VisaStatus: string implements HasColor, HasIcon, HasLabel
{
    case NOT_APPLIED = 'not_applied';
    case APPLIED = 'applied';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case EXPIRED = 'expired';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::NOT_APPLIED => 'gray',
            self::APPLIED => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
            self::EXPIRED => 'gray',
        };
    }

    public function getIcon(): string|\BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::NOT_APPLIED => 'heroicon-o-minus-circle',
            self::APPLIED => 'heroicon-o-paper-airplane',
            self::APPROVED => 'heroicon-o-check-badge',
            self::REJECTED => 'heroicon-o-x-mark',
            self::EXPIRED => 'heroicon-o-exclamation-circle',
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::NOT_APPLIED => 'لم يتقدم',
            self::APPLIED => 'تم التقديم',
            self::APPROVED => 'موافق عليها',
            self::REJECTED => 'مرفوضة',
            self::EXPIRED => 'منتهية',
        };
    }
}
