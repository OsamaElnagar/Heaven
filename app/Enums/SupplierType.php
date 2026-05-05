<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum SupplierType: string implements HasColor, HasIcon, HasLabel
{
    case HOTEL = 'hotel';
    case AIRLINE = 'airline';
    case TRANSPORT = 'transport';
    case CATERING = 'catering';
    case OTHER = 'other';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::HOTEL => 'warning',
            self::AIRLINE => 'info',
            self::TRANSPORT => 'success',
            self::CATERING => 'danger',
            self::OTHER => 'gray',
        };
    }

    public function getIcon(): string|\BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::HOTEL => 'heroicon-o-building-office-2',
            self::AIRLINE => 'heroicon-o-paper-airplane',
            self::TRANSPORT => 'heroicon-o-truck',
            self::CATERING => 'heroicon-o-cake',
            self::OTHER => 'heroicon-o-ellipsis-horizontal-circle',
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::HOTEL => 'فندق',
            self::AIRLINE => 'شركة طيران',
            self::TRANSPORT => 'نقل',
            self::CATERING => 'تموين',
            self::OTHER => 'أخرى',
        };
    }
}
