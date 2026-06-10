<?php

namespace App\Filament\Widgets;

use App\Enums\TripStatus;
use App\Models\Trip;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UpcomingTripsWidget extends BaseWidget
{
    protected static ?string $heading = 'الرحلات القادمة';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Trip::whereIn('status', [TripStatus::UPCOMING, TripStatus::DEPARTED])
                    ->with('package')
                    ->withCount('bookings')
                    ->orderBy('departure_at', 'asc')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('name')
                    ->label('الرحلة')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('package.name')
                    ->label('الباقة')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge(),

                TextColumn::make('departure_at')
                    ->label('تاريخ المغادرة')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('bookings_count')
                    ->label('الحجوزات')
                    ->numeric()
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
