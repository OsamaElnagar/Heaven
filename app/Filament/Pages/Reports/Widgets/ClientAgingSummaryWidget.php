<?php

namespace App\Filament\Pages\Reports\Widgets;

use App\Models\Booking;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class ClientAgingSummaryWidget extends BaseWidget
{
    protected ?string $pollingInterval = null;

    public ?string $asOfDate = null;

    public ?int $clientId = null;

    public function mount(): void
    {
        $this->asOfDate ??= now()->format('Y-m-d');
    }

    public function getSummary(): array
    {
        return static::computeSummary($this->asOfDate, $this->clientId);
    }

    public static function computeSummary(?string $asOfDate, ?int $clientId): array
    {
        $asOf = $asOfDate ? Carbon::parse($asOfDate) : Carbon::now();

        $query = Booking::query()
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereColumn('net_price', '>', 'paid_amount')
            ->when($clientId, fn ($q) => $q->where('client_id', $clientId));

        $total = 0;
        $buckets = ['0-30 يوم' => 0, '30-60 يوم' => 0, '60-90 يوم' => 0, 'أكثر من 90 يوم' => 0];

        $bookings = $query->get(['net_price', 'paid_amount', 'due_date']);
        foreach ($bookings as $booking) {
            $outstanding = max(0, (int) $booking->net_price - (int) $booking->paid_amount);
            if ($outstanding <= 0) {
                continue;
            }
            $total += $outstanding;

            $dueDate = $booking->due_date;
            $days = 0;
            if ($dueDate && $dueDate->lessThan($asOf->startOfDay())) {
                $days = (int) $asOf->startOfDay()->diffInDays($dueDate->startOfDay());
            }

            $bucket = match (true) {
                $days <= 30 => '0-30 يوم',
                $days <= 60 => '30-60 يوم',
                $days <= 90 => '60-90 يوم',
                default => 'أكثر من 90 يوم',
            };
            $buckets[$bucket] += $outstanding;
        }

        return [
            'total_outstanding' => $total,
            'count' => $bookings->count(),
            'buckets' => $buckets,
        ];
    }

    protected function getStats(): array
    {
        $summary = $this->getSummary();

        return [
            Stat::make('إجمالي المستحق على العملاء', number_format($summary['total_outstanding']).' ج.م')
                ->description('مجموع مبالغ الحجوزات غير المدفوعة')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('عدد الحجوزات', number_format($summary['count']))
                ->description('حجوزات معلقة أو مؤكدة بمستحقات')
                ->descriptionIcon('heroicon-m-list-bullet')
                ->color('info'),
            Stat::make('0-30 يوم', number_format($summary['buckets']['0-30 يوم']).' ج.م')
                ->description('مستحقات حديثة')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            Stat::make('30-60 يوم', number_format($summary['buckets']['30-60 يوم']).' ج.م')
                ->description('مستحقات متأخرة قليلاً')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('warning'),
            Stat::make('60-90 يوم', number_format($summary['buckets']['60-90 يوم']).' ج.م')
                ->description('مستحقات متأخرة')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
            Stat::make('أكثر من 90 يوم', number_format($summary['buckets']['أكثر من 90 يوم']).' ج.م')
                ->description('مستحقات قديمة جدًا')
                ->descriptionIcon('heroicon-m-shield-exclamation')
                ->color('gray'),
        ];
    }
}
