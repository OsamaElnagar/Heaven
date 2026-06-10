<?php

namespace App\Filament\Pages\Reports\Widgets;

use App\Enums\AccountClass;
use App\Enums\CashFlowCategory;
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
        $receipts = ReceiptVoucher::query()
            ->where('status', 'posted')
            ->when($dateFrom, fn ($q) => $q->whereDate('voucher_date', '>=', Carbon::parse($dateFrom)))
            ->when($dateTo, fn ($q) => $q->whereDate('voucher_date', '<=', Carbon::parse($dateTo)))
            ->with('journalEntry.lines.account')
            ->get();

        $payments = PaymentVoucher::query()
            ->where('status', 'posted')
            ->when($dateFrom, fn ($q) => $q->whereDate('voucher_date', '>=', Carbon::parse($dateFrom)))
            ->when($dateTo, fn ($q) => $q->whereDate('voucher_date', '<=', Carbon::parse($dateTo)))
            ->with('journalEntry.lines.account')
            ->get();

        $operatingIn = 0;
        $operatingOut = 0;
        $investingIn = 0;
        $investingOut = 0;
        $financingIn = 0;
        $financingOut = 0;

        foreach ($receipts as $receipt) {
            $category = self::resolveCategory($receipt);
            $amount = (int) $receipt->amount;

            match ($category) {
                CashFlowCategory::OPERATING => $operatingIn += $amount,
                CashFlowCategory::INVESTING => $investingIn += $amount,
                CashFlowCategory::FINANCING => $financingIn += $amount,
            };
        }

        foreach ($payments as $payment) {
            $category = self::resolveCategory($payment);
            $amount = (int) $payment->amount;

            match ($category) {
                CashFlowCategory::OPERATING => $operatingOut += $amount,
                CashFlowCategory::INVESTING => $investingOut += $amount,
                CashFlowCategory::FINANCING => $financingOut += $amount,
            };
        }

        return [
            'operating_in' => $operatingIn,
            'operating_out' => $operatingOut,
            'operating_net' => $operatingIn - $operatingOut,
            'investing_in' => $investingIn,
            'investing_out' => $investingOut,
            'investing_net' => $investingIn - $investingOut,
            'financing_in' => $financingIn,
            'financing_out' => $financingOut,
            'financing_net' => $financingIn - $financingOut,
            'total_net' => ($operatingIn - $operatingOut) + ($investingIn - $investingOut) + ($financingIn - $financingOut),
        ];
    }

    protected static function resolveCategory($voucher): CashFlowCategory
    {
        if (! $voucher->journalEntry) {
            return CashFlowCategory::OPERATING;
        }

        $accountClasses = $voucher->journalEntry->lines
            ->pluck('account.class')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $hasRevenueOrExpense = collect($accountClasses)->contains(fn ($c) => in_array($c, [
            AccountClass::REVENUE->value,
            AccountClass::EXPENSES->value,
        ]));

        $hasLiabilityOrEquity = collect($accountClasses)->contains(fn ($c) => in_array($c, [
            AccountClass::LIABILITIES->value,
            AccountClass::EQUITY->value,
        ]));

        $hasNonCashAsset = collect($accountClasses)->contains(AccountClass::ASSETS->value)
            && ! $voucher->journalEntry->lines->every(fn ($l) => in_array($l->account?->code, ['1110', '1120', '1130']));

        if ($hasLiabilityOrEquity) {
            return CashFlowCategory::FINANCING;
        }

        if ($hasNonCashAsset) {
            return CashFlowCategory::INVESTING;
        }

        return CashFlowCategory::OPERATING;
    }

    protected function getStats(): array
    {
        $summary = $this->getSummary();

        return [
            Stat::make('الأنشطة التشغيلية - وارد', number_format($summary['operating_in']).' ج.م')
                ->description('إجمالي سندات القبض التشغيلية')
                ->descriptionIcon('heroicon-m-arrow-down-circle')
                ->color('success'),
            Stat::make('الأنشطة التشغيلية - صادر', number_format($summary['operating_out']).' ج.م')
                ->description('إجمالي سندات الصرف التشغيلية')
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
