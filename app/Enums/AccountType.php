<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum AccountType: string implements HasColor, HasIcon, HasLabel
{
    case HEADER = 'header';
    case DETAIL = 'detail';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::HEADER => 'gray',
            self::DETAIL => 'primary',
        };
    }

    public function getIcon(): string|\BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::HEADER => 'heroicon-o-folder',
            self::DETAIL => 'heroicon-o-document-text',
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::HEADER => 'مجمّع',
            self::DETAIL => 'تفصيلي',
        };
    }
}
