<?php

namespace App\Filament\Resources\Trips\Pages;

use App\Filament\Resources\Trips\TripResource;
use App\Services\ReportService;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TripDashboardPage extends Page
{
    use InteractsWithRecord;

    protected static string $resource = TripResource::class;

    protected static ?string $title = 'لوحة معلومات الرحلة';

    protected string $view = 'filament.resources.trips.pages.trip-dashboard';

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

    public function infolist(Schema $schema): Schema
    {
        $totalBookings = $this->record->bookings()->count();
        $confirmedBookings = $this->record->bookings()->where('status', 'confirmed')->count();
        $totalPaid = $this->record->bookings()->sum('paid_amount');
        $totalNet = $this->record->bookings()->sum('net_price');
        $visas = (new ReportService)->visaDashboard($this->record);

        return $schema
            ->state([])
            ->schema([
                Section::make('إحصائيات الحجوزات')
                    ->schema([
                        TextEntry::make('total_bookings')
                            ->label('إجمالي الحجوزات')
                            ->state($totalBookings),
                        TextEntry::make('confirmed_bookings')
                            ->label('الحجوزات المؤكدة')
                            ->state($confirmedBookings),
                        TextEntry::make('total_paid')
                            ->label('المبلغ المحصل')
                            ->state(number_format($totalPaid, 2).' ج.م'),
                        TextEntry::make('total_outstanding')
                            ->label('المبلغ المستحق')
                            ->state(number_format(max($totalNet - $totalPaid, 0), 2).' ج.م'),
                    ])
                    ->columns(4),

                Section::make('حالة التأشيرات')
                    ->schema(
                        collect($visas)->map(
                            fn ($count, $status) => TextEntry::make('visa_'.$status)
                                ->label($status)
                                ->state($count)
                                ->badge()
                        )->toArray()
                    )
                    ->columns(5),
            ]);
    }

    protected function getViewData(): array
    {
        $visas = (new ReportService)->visaDashboard($this->record);
        $occupancy = (new ReportService)->occupancyReport($this->record);

        return [
            'visas' => $visas,
            'occupancy' => $occupancy,
        ];
    }
}
