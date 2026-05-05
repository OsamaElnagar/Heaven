<?php

namespace App\Filament\Resources\Trips\Pages;

use App\Filament\Resources\Trips\TripResource;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class TripRoomingPage extends Page implements HasTable
{
    use InteractsWithRecord;
    use InteractsWithTable;

    protected static string $resource = TripResource::class;

    protected static ?string $title = 'توزيع الغرف';

    protected string $view = 'filament.resources.trips.pages.trip-rooming';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    public function getBreadcrumbs(): array
    {
        $resource = static::getResource();

        return [
            $resource::getUrl('index') => $resource::getBreadcrumb(),
            $resource::getUrl('view', ['record' => $this->record]) => $this->record->name,
            '#' => static::$title,
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn () => $this->record->rooms()->with(['hotel', 'bookings.client'])
            )
            ->columns([
                TextColumn::make('room_number')
                    ->label('رقم الغرفة')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('النوع')
                    ->badge(),
                TextColumn::make('hotel.name')
                    ->label('الفندق')
                    ->searchable(),
                TextColumn::make('occupied')
                    ->label('الإشغال')
                    ->formatStateUsing(fn ($state, $record) => $state.'/'.$record->capacity),
                TextColumn::make('bookings_count')
                    ->label('عدد النزلاء')
                    ->counts('bookings'),
            ])
            ->recordUrl(fn ($record): string => TripResource::getUrl('view', ['record' => $this->record->id]));
    }
}
