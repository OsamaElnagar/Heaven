<?php

namespace App\Filament\Widgets;

use App\Enums\TripStatus;
use App\Models\Trip;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class UpcomingTripsWidget extends BaseWidget
{
    protected static ?string $heading = 'الرحلات القادمة';

    protected int|string|array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        return Trip::whereIn('status', [TripStatus::UPCOMING, TripStatus::DEPARTED])
            ->with('package')
            ->withCount('bookings')
            ->latest('departure_at')
            ->limit(5);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
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
                    ->label('موعد المغادرة')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('bookings_count')
                    ->label('الحجوزات')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('package.reserved_seats')
                    ->label('المقاعد المحجوزة')
                    ->numeric(),
            ])
            ->paginated(false);
    }
}
