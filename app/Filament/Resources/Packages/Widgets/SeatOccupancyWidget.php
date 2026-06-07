<?php

namespace App\Filament\Resources\Packages\Widgets;

use App\Enums\BookingStatus;
use App\Models\Package;
use Filament\Widgets\ChartWidget;

class SeatOccupancyWidget extends ChartWidget
{
    protected ?string $heading = 'المقاعد المحجوزة - نشط فقط';

    protected string $color = 'info';

    protected int|string|array $columnSpan = 'full';

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $packages = Package::where('is_active', true)
            ->where('season_year', '>=', now()->year)
            ->get();

        $labels = [];
        $confirmed = [];
        $available = [];

        foreach ($packages as $pkg) {
            $labels[] = $pkg->name;
            $confirmed[] = (int) $pkg->bookings()->where('status', BookingStatus::CONFIRMED)->count();
            $available[] = max(0, $pkg->total_seats - $pkg->reserved_seats);
        }

        return [
            'datasets' => [
                [
                    'label' => 'محجوز',
                    'data' => $confirmed,
                    'backgroundColor' => ['#10b981', '#34d399', '#6ee7b7', '#a7f3d0', '#d1fae5', '#ecfdf5', '#f0fdf4', '#fef3c7'],
                ],
                [
                    'label' => 'متاح',
                    'data' => $available,
                    'backgroundColor' => ['#d1d5db', '#d1d5db', '#d1d5db', '#d1d5db', '#d1d5db', '#d1d5db', '#d1d5db', '#d1d5db'],
                ],
            ],
            'labels' => $labels,
        ];
    }
}
