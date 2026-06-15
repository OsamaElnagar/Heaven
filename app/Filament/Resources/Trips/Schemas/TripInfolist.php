<?php

namespace App\Filament\Resources\Trips\Schemas;

use App\Enums\BookingStatus;
use App\Filament\Resources\Packages\PackageResource;
use App\Models\Trip;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class TripInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات الرحلة')
                    ->components([
                        TextEntry::make('name')
                            ->label('اسم الرحلة'),
                        TextEntry::make('package.name')
                            ->label('الباقة')
                            ->url(fn ($record) => $record->package
                                ? PackageResource::getUrl('edit', ['record' => $record->package])
                                : null, true)
                            ->icon(Heroicon::ArrowUpRight)
                            ->color('primary'),
                        TextEntry::make('status')
                            ->label('الحالة')
                            ->badge(),
                        TextEntry::make('airline')
                            ->label('شركة الطيران')
                            ->placeholder('—'),
                        TextEntry::make('flight_number')
                            ->label('رقم الرحلة')
                            ->placeholder('—'),
                    ])
                    ->columns(2),

                Section::make('مواعيد السفر')
                    ->components([
                        TextEntry::make('departure_at')
                            ->label('موعد المغادرة')
                            ->dateTime()
                            ->placeholder('—'),
                        TextEntry::make('return_at')
                            ->label('موعد العودة')
                            ->dateTime()
                            ->placeholder('—'),
                        TextEntry::make('departure_airport')
                            ->label('مطار المغادرة')
                            ->placeholder('—'),
                    ])
                    ->columns(2),

                Section::make('إحصائيات الحجوزات')
                    ->components([
                        TextEntry::make('total_bookings')
                            ->label('إجمالي الحجوزات')
                            ->state(fn (Trip $record) => $record->bookings()->count()),
                        TextEntry::make('confirmed_bookings')
                            ->label('الحجوزات المؤكدة')
                            ->state(fn (Trip $record) => $record->bookings()
                                ->where('bookings.status', BookingStatus::CONFIRMED->value)
                                ->count()),
                        TextEntry::make('total_paid')
                            ->label('المبلغ المحصل')
                            ->state(fn (Trip $record) => number_format(
                                $record->bookings()->sum('paid_amount'), 2
                            ).' ج.م'),
                        TextEntry::make('total_outstanding')
                            ->label('المبلغ المستحق')
                            ->state(fn (Trip $record) => number_format(
                                max($record->bookings()->sum('net_price') - $record->bookings()->sum('paid_amount'), 0), 2
                            ).' ج.م'),
                    ])
                    ->columns(4),
            ]);
    }
}
