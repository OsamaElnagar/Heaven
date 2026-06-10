<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum ExpenseClass: string implements HasLabel
{
    case DIRECT_COSTS = 'direct_costs';
    case OPERATING_EXPENSES = 'operating_expenses';

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::DIRECT_COSTS => 'تكاليف مباشرة',
            self::OPERATING_EXPENSES => 'مصروفات تشغيلية',
        };
    }

    public function matchesCode(?string $code): bool
    {
        if ($code === null) {
            return false;
        }

        return match ($this) {
            self::DIRECT_COSTS => str_starts_with($code, '5') && ! str_starts_with($code, '52'),
            self::OPERATING_EXPENSES => str_starts_with($code, '52'),
        };
    }
}
