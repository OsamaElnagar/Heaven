<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum PayeeType: string implements HasColor, HasIcon, HasLabel
{
    case CLIENT = 'client';
    case SUPPLIER = 'supplier';
    case EMPLOYEE = 'employee';
    case OTHER = 'other';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::CLIENT => 'info',
            self::SUPPLIER => 'warning',
            self::EMPLOYEE => 'success',
            self::OTHER => 'gray',
        };
    }

    public function getIcon(): string|\BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::CLIENT => 'heroicon-o-user-group',
            self::SUPPLIER => 'heroicon-o-shopping-cart',
            self::EMPLOYEE => 'heroicon-o-user',
            self::OTHER => 'heroicon-o-ellipsis-horizontal',
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::CLIENT => 'عميل',
            self::SUPPLIER => 'مورد',
            self::EMPLOYEE => 'موظف',
            self::OTHER => 'أخرى',
        };
    }
}
