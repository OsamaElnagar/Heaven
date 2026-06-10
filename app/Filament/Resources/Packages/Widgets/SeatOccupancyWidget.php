<?php

namespace App\Filament\Resources\Packages\Widgets;

use App\Enums\BookingStatus;
use App\Models\Package;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class SeatOccupancyWidget extends ChartWidget
{
    protected ?string $heading = 'المقاعد المحجوزة - نشط فقط';

    protected ?string $maxHeight = '400px';

    protected string $color = 'info';

    protected int|string|array $columnSpan = 'full';

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $data = Cache::remember('seat_occupancy_chart', 300, function () {
            $packages = Package::where('is_active', true)
                ->where('season_year', '>=', now()->year)
                ->withCount(['bookings as confirmed_bookings_count' => fn ($query) => $query->where('status', BookingStatus::CONFIRMED)])
                ->get();

            $labels = [];
            $confirmed = [];
            $available = [];

            foreach ($packages as $pkg) {
                $labels[] = $pkg->name;
                $confirmed[] = (int) $pkg->confirmed_bookings_count;
                $available[] = max(0, $pkg->total_seats - $pkg->reserved_seats);
            }

            return compact('labels', 'confirmed', 'available');
        });

        $count = count($data['labels']);
        $greens = ['#10b981', '#34d399', '#6ee7b7', '#a7f3d0', '#d1fae5', '#ecfdf5', '#f0fdf4', '#fef3c7'];
        $grays = array_fill(0, $count, '#d1d5db');

        $greenColors = [];
        for ($i = 0; $i < $count; $i++) {
            $greenColors[] = $greens[$i % count($greens)];
        }

        return [
            'datasets' => [
                [
                    'label' => 'محجوز',
                    'data' => $data['confirmed'],
                    'backgroundColor' => $greenColors,
                ],
                [
                    'label' => 'متاح',
                    'data' => $data['available'],
                    'backgroundColor' => $grays,
                ],
            ],
            'labels' => $data['labels'],
        ];
    }
}
