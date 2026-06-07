<?php

namespace App\Filament\Pages\Reports\Widgets;

use App\Models\Supplier;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class SupplierBalancesSummaryWidget extends BaseWidget
{
    protected ?string $pollingInterval = null;

    public function getSummary(): array
    {
        return static::computeSummary();
    }

    public static function computeSummary(): array
    {
        $suppliers = Supplier::query()
            ->whereNotNull('account_id')
            ->get(['id', 'account_id']);

        $accountIds = $suppliers->pluck('account_id')->filter()->all();

        $balances = DB::table('journal_lines as jl')
            ->join('journal_entries as je', 'jl.journal_entry_id', '=', 'je.id')
            ->where('je.status', 'posted')
            ->whereNull('je.deleted_at')
            ->whereIn('jl.account_id', $accountIds)
            ->groupBy('jl.account_id')
            ->selectRaw('jl.account_id, SUM(jl.credit_amount) - SUM(jl.debit_amount) as balance')
            ->pluck('balance', 'account_id');

        $totalOwed = 0;
        $creditCount = 0;
        $debitCount = 0;

        foreach ($balances as $balance) {
            $b = (int) $balance;
            if ($b > 0) {
                $totalOwed += $b;
                $creditCount++;
            } elseif ($b < 0) {
                $debitCount++;
            }
        }

        return [
            'total_owed' => $totalOwed,
            'credit_count' => $creditCount,
            'debit_count' => $debitCount,
            'suppliers_count' => $suppliers->count(),
        ];
    }

    protected function getStats(): array
    {
        $summary = $this->getSummary();

        return [
            Stat::make('إجمالي المستحق للموردين', number_format($summary['total_owed']).' ج.م')
                ->description('مجموع أرصدة الموردين الدائنة')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('warning'),
            Stat::make('موردين لهم رصيد دائن', number_format($summary['credit_count']))
                ->description('موردين تستحق لهم مبالغ')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),
            Stat::make('موردين رصيدهم مدين', number_format($summary['debit_count']))
                ->description('موردين مدينين لنا')
                ->descriptionIcon('heroicon-m-arrow-uturn-left')
                ->color('success'),
            Stat::make('إجمالي الموردين', number_format($summary['suppliers_count']))
                ->description('عدد الموردين النشطين')
                ->descriptionIcon('heroicon-m-list-bullet')
                ->color('gray'),
        ];
    }
}
