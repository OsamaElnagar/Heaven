<?php

namespace App\Filament\Components\Filters;

use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Illuminate\Database\Eloquent\Builder;

class DateRangeFilter
{
    public static function make(string $name = 'date', ?string $column = null, string $label = 'التاريخ'): Filter
    {
        $filter = Filter::make($name)
            ->label($label)
            ->schema([
                DatePicker::make('date_from')
                    ->label('من تاريخ')
                    ->displayFormat('Y-m-d')
                    ->native(false),
                DatePicker::make('date_to')
                    ->label('إلى تاريخ')
                    ->displayFormat('Y-m-d')
                    ->native(false),
            ])
            ->query(fn (Builder $query) => $query)
            ->indicateUsing(function (array $data): array {
                $indicators = [];

                if ($data['date_from'] ?? null) {
                    $indicators[] = Indicator::make('من '.Carbon::parse($data['date_from'])->format('Y-m-d'))
                        ->removeField('date_from');
                }

                if ($data['date_to'] ?? null) {
                    $indicators[] = Indicator::make('إلى '.Carbon::parse($data['date_to'])->format('Y-m-d'))
                        ->removeField('date_to');
                }

                return $indicators;
            });

        if ($column) {
            $filter->query(function (Builder $query, array $data) use ($column): Builder {
                return $query
                    ->when(
                        $data['date_from'],
                        fn (Builder $query, $date): Builder => $query->whereDate($column, '>=', Carbon::parse($date)),
                    )
                    ->when(
                        $data['date_to'],
                        fn (Builder $query, $date): Builder => $query->whereDate($column, '<=', Carbon::parse($date)),
                    );
            });
        }

        return $filter;
    }
}
