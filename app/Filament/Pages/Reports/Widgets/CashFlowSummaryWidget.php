<?php

namespace App\Filament\Pages\Reports\Widgets;

use App\Models\PaymentVoucher;
use App\Models\ReceiptVoucher;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class CashFlowSummaryWidget extends BaseWidget
{
    protected ?string $pollingInterval = null;

    public ?string $dateFrom = null;

    public ?string $dateTo = null;

    public function getSummary(): array
    {
        return static::computeSummary($this->dateFrom, $this->dateTo);
    }

    public static function computeSummary(?string $dateFrom, ?string $dateTo): array
    {
        $rows = collect();
        $rows = $rows->merge(
            ReceiptVoucher::query()
                ->where('status', 'posted')
                ->when($dateFrom, fn ($q) => $q->whereDate('voucher_date', '>=', Carbon::parse($dateFrom)))
                ->when($dateTo, fn ($q) => $q->whereDate('voucher_date', '<=', Carbon::parse($dateTo)))
                ->get(['voucher_date', 'amount'])
                ->map(fn ($r) => ['direction' => 'وارد نقدي', 'amount' => (int) $r->amount])
        );
        $rows = $rows->merge(
            PaymentVoucher::query()
                ->where('status', 'posted')
                ->when($dateFrom, fn ($q) => $q->whereDate('voucher_date', '>=', Carbon::parse($dateFrom)))
                ->when($dateTo, fn ($q) => $q->whereDate('voucher_date', '<=', Carbon::parse($dateTo)))
                ->get(['voucher_date', 'amount'])
                ->map(fn ($r) => ['direction' => 'صادر نقدي', 'amount' => (int) $r->amount])
        );

        $calcNet = fn ($set) => (int) $set->where('direction', 'وارد نقدي')->sum('amount')
            - (int) $set->where('direction', 'صادر نقدي')->sum('amount');

        $operating = $rows;
        $financing = collect();

        return [
            'operating_in' => (int) $operating->where('direction', 'وارد نقدي')->sum('amount'),
            'operating_out' => (int) $operating->where('direction', 'صادر نقدي')->sum('amount'),
            'operating_net' => $calcNet($operating),
            'financing_in' => (int) $financing->where('direction', 'وارد نقدي')->sum('amount'),
            'financing_out' => (int) $financing->where('direction', 'صادر نقدي')->sum('amount'),
            'financing_net' => $calcNet($financing),
            'total_net' => $calcNet($rows),
        ];
    }

    protected function getStats(): array
    {
        $summary = $this->getSummary();

        return [
            Stat::make('الأنشطة التشغيلية - وارد', number_format($summary['operating_in']).' ج.م')
                ->description('إجمالي سندات القبض')
                ->descriptionIcon('heroicon-m-arrow-down-circle')
                ->color('success'),
            Stat::make('الأنشطة التشغيلية - صادر', number_format($summary['operating_out']).' ج.م')
                ->description('إجمالي سندات الصرف')
                ->descriptionIcon('heroicon-m-arrow-up-circle')
                ->color('danger'),
            Stat::make('صافي التدفق التشغيلي', number_format($summary['operating_net']).' ج.م')
                ->description('فرق الوارد والصادر التشغيلي')
                ->descriptionIcon('heroicon-m-arrows-right-left')
                ->color($summary['operating_net'] >= 0 ? 'success' : 'danger'),
            Stat::make('صافي التدفق النقدي', number_format($summary['total_net']).' ج.م')
                ->description('مجموع كل التدفقات النقدية')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($summary['total_net'] >= 0 ? 'success' : 'danger'),
        ];
    }
}
