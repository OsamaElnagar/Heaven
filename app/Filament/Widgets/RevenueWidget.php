<?php

namespace App\Filament\Widgets;

use App\Enums\BookingStatus;
use App\Enums\ExpenseStatus;
use App\Models\Booking;
use App\Models\ReceiptVoucher;
use App\Models\RefundVoucher;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RevenueWidget extends BaseWidget
{
    protected ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $year = now()->year;
        $cacheKey = "dashboard_revenue_{$year}";

        $data = Cache::remember($cacheKey, 300, function () use ($year) {
            $totalPaid = (int) ReceiptVoucher::where('status', ExpenseStatus::POSTED)
                ->whereYear('voucher_date', $year)
                ->sum('amount');

            $totalRefunded = (int) RefundVoucher::where('status', ExpenseStatus::POSTED)
                ->whereYear('voucher_date', $year)
                ->sum('amount');

            $totalOutstanding = (int) Booking::whereIn('status', [BookingStatus::PENDING, BookingStatus::CONFIRMED])
                ->whereYear('created_at', $year)
                ->sum(DB::raw('net_price - paid_amount'));

            $avgBooking = (int) (Booking::whereYear('created_at', $year)->avg('net_price') ?? 0);

            $monthlyPaid = ReceiptVoucher::where('status', ExpenseStatus::POSTED)
                ->whereYear('voucher_date', $year)
                ->selectRaw("CAST(strftime('%m', voucher_date) AS INTEGER) as month, SUM(amount) as total")
                ->groupBy('month')
                ->pluck('total', 'month')
                ->toArray();

            $monthlyOutstanding = Booking::whereIn('status', [BookingStatus::PENDING, BookingStatus::CONFIRMED])
                ->whereYear('created_at', $year)
                ->selectRaw("CAST(strftime('%m', created_at) AS INTEGER) as month, SUM(net_price - paid_amount) as total")
                ->groupBy('month')
                ->pluck('total', 'month')
                ->toArray();

            $monthlyRefunded = RefundVoucher::where('status', ExpenseStatus::POSTED)
                ->whereYear('voucher_date', $year)
                ->selectRaw("CAST(strftime('%m', voucher_date) AS INTEGER) as month, SUM(amount) as total")
                ->groupBy('month')
                ->pluck('total', 'month')
                ->toArray();

            $monthlyAvg = Booking::whereYear('created_at', $year)
                ->selectRaw("CAST(strftime('%m', created_at) AS INTEGER) as month, AVG(net_price) as avg")
                ->groupBy('month')
                ->pluck('avg', 'month')
                ->toArray();

            return compact(
                'totalPaid', 'totalRefunded', 'totalOutstanding', 'avgBooking',
                'monthlyPaid', 'monthlyOutstanding', 'monthlyRefunded', 'monthlyAvg'
            );
        });

        $buildSparkline = fn (array $monthly): array => array_map(
            fn ($m) => (float) ($monthly[$m] ?? 0),
            range(1, 12),
        );

        return [
            Stat::make('إجمالي المحصل', number_format($data['totalPaid'], 0).' ج.م')
                ->description('المدفوعات المستلمة')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('success')
                ->chart($buildSparkline($data['monthlyPaid'])),

            Stat::make('المستحق', number_format($data['totalOutstanding'], 0).' ج.م')
                ->description('متأخرات')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color('danger')
                ->chart($buildSparkline($data['monthlyOutstanding'])),

            Stat::make('المسترجع', number_format($data['totalRefunded'], 0).' ج.م')
                ->description('مبالغ مستردة')
                ->descriptionIcon('heroicon-o-arrow-uturn-left')
                ->color('gray')
                ->chart($buildSparkline($data['monthlyRefunded'])),

            Stat::make('متوسط قيمة الحجز', number_format($data['avgBooking'], 0).' ج.م')
                ->description('متوسط السعر')
                ->descriptionIcon('heroicon-o-chart-bar')
                ->color('info')
                ->chart($buildSparkline($data['monthlyAvg'])),
        ];
    }
}
