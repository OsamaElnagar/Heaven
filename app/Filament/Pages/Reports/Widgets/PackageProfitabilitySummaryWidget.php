<?php

namespace App\Filament\Pages\Reports\Widgets;

use App\Enums\ExpenseStatus;
use App\Models\Package;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PackageProfitabilitySummaryWidget extends BaseWidget
{
    protected ?string $pollingInterval = null;

    public ?string $type = null;

    public function getSummary(): array
    {
        return static::computeSummary($this->type);
    }

    public static function computeSummary(?string $type): array
    {
        $query = Package::query()
            ->with(['bookings.receiptVouchers', 'bookings.refundVouchers']);

        if ($type) {
            $query->where('type_id', $type);
        }

        $totalPackages = 0;
        $totalBookings = 0;
        $totalCollected = 0;
        $totalOutstanding = 0;

        foreach ($query->get() as $package) {
            $totalPackages++;
            foreach ($package->bookings as $booking) {
                $totalBookings++;
                $received = (int) $booking->receiptVouchers
                    ->where('status', ExpenseStatus::POSTED->value)
                    ->sum('amount');
                $refunded = (int) $booking->refundVouchers
                    ->where('status', ExpenseStatus::POSTED->value)
                    ->sum('amount');
                $netPaid = $received - $refunded;
                $netPrice = (int) $booking->net_price;

                $totalCollected += $netPaid;
                $totalOutstanding += max($netPrice - $netPaid, 0);
            }
        }

        return [
            'total_packages' => $totalPackages,
            'total_bookings' => $totalBookings,
            'total_collected' => $totalCollected,
            'total_outstanding' => $totalOutstanding,
            'total_revenue' => $totalCollected + $totalOutstanding,
        ];
    }

    protected function getStats(): array
    {
        $summary = $this->getSummary();

        return [
            Stat::make('عدد الباقات', number_format($summary['total_packages']))
                ->description('باقات مع حجوزات')
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('info'),
            Stat::make('عدد الحجوزات', number_format($summary['total_bookings']))
                ->description('إجمالي الحجوزات على الباقات')
                ->descriptionIcon('heroicon-m-list-bullet')
                ->color('info'),
            Stat::make('إجمالي المحصل', number_format($summary['total_collected']).' ج.م')
                ->description('صافي المقبوضات (القبض - الاسترداد)')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
            Stat::make('المستحق', number_format($summary['total_outstanding']).' ج.م')
                ->description('مبالغ لم تُحصّل بعد')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('إجمالي الإيرادات', number_format($summary['total_revenue']).' ج.م')
                ->description('المحصل + المستحق')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary'),
        ];
    }
}
