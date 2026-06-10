<?php

namespace App\Filament\Widgets;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentBookingsWidget extends BaseWidget
{
    protected static ?string $heading = 'آخر الحجوزات';

    protected int|string|array $columnSpan = 'full';

    protected function query(): Builder
    {
        return Booking::with('client', 'package')
            ->whereIn('status', [BookingStatus::PENDING, BookingStatus::CONFIRMED, BookingStatus::COMPLETED])
            ->latest()
            ->limit(10);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->query())
            ->columns([
                TextColumn::make('reference')
                    ->label('المرجع')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('client.name')
                    ->label('العميل')
                    ->searchable(),

                TextColumn::make('package.name')
                    ->label('الباقة'),

                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge(),

                TextColumn::make('net_price')
                    ->label('الصافي')
                    ->money(config('app.currency'), locale: config('app.currency_locale'), decimalPlaces: 0),

                TextColumn::make('paid_amount')
                    ->label('المدفوع')
                    ->money(config('app.currency'), locale: config('app.currency_locale'), decimalPlaces: 0),

                TextColumn::make('created_at')
                    ->label('التاريخ')
                    ->dateTime()
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
