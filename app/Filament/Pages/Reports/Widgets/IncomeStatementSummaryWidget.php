<?php

namespace App\Filament\Pages\Reports\Widgets;

use App\Enums\AccountClass;
use App\Enums\ExpenseClass;
use App\Models\Account;
use App\Models\FiscalYear;
use App\Models\JournalLine;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class IncomeStatementSummaryWidget extends BaseWidget
{
    protected ?string $pollingInterval = null;

    public ?int $fiscalYearId = null;

    public ?string $dateFrom = null;

    public ?string $dateTo = null;

    public function mount(): void
    {
        $this->fiscalYearId ??= FiscalYear::where('status', 'open')->value('id');
    }

    public static function computeSummary(?int $fiscalYearId, ?string $dateFrom, ?string $dateTo): array
    {
        $periodSub = JournalLine::periodAggregate($fiscalYearId, $dateFrom, $dateTo);

        $records = Account::query()
            ->whereIn('class', [AccountClass::REVENUE, AccountClass::EXPENSES])
            ->where('is_active', true)
            ->where('type', 'detail')
            ->leftJoinSub($periodSub, 'period', fn ($join) => $join->on('accounts.id', '=', 'period.account_id'))
            ->select('accounts.*')
            ->selectRaw('
                COALESCE(period.period_debit, 0) as total_debit,
                COALESCE(period.period_credit, 0) as total_credit
            ')
            ->whereNull('accounts.deleted_at')
            ->get();

        $totalRevenue = $records->where('class', AccountClass::REVENUE->value)
            ->sum(fn ($r) => (int) $r->total_credit - (int) $r->total_debit);

        $totalExpenses = $records->where('class', AccountClass::EXPENSES->value)
            ->sum(fn ($r) => (int) $r->total_debit - (int) $r->total_credit);

        $totalDirectCosts = $records->where('class', AccountClass::EXPENSES->value)
            ->filter(fn ($r) => ExpenseClass::DIRECT_COSTS->matchesCode($r->code))
            ->sum(fn ($r) => (int) $r->total_debit - (int) $r->total_credit);

        $totalOpEx = $records->where('class', AccountClass::EXPENSES->value)
            ->filter(fn ($r) => ExpenseClass::OPERATING_EXPENSES->matchesCode($r->code))
            ->sum(fn ($r) => (int) $r->total_debit - (int) $r->total_credit);

        return [
            'total_revenue' => $totalRevenue,
            'total_direct_costs' => $totalDirectCosts,
            'gross_profit' => $totalRevenue - $totalDirectCosts,
            'total_opex' => $totalOpEx,
            'total_expenses' => $totalExpenses,
            'net_income' => $totalRevenue - $totalExpenses,
        ];
    }

    public function getSummary(): array
    {
        return static::computeSummary($this->fiscalYearId, $this->dateFrom, $this->dateTo);
    }

    protected function getStats(): array
    {
        $summary = $this->getSummary();
        $netIncomeColor = $summary['net_income'] >= 0 ? 'success' : 'danger';
        $grossProfitColor = $summary['gross_profit'] >= 0 ? 'success' : 'danger';

        return [
            Stat::make('إجمالي الإيرادات', number_format($summary['total_revenue']).' ج.م')
                ->description('مجموع أرصدة حسابات الإيرادات')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('التكاليف المباشرة', number_format($summary['total_direct_costs']).' ج.م')
                ->description(ExpenseClass::DIRECT_COSTS->getLabel())
                ->descriptionIcon('heroicon-m-cog')
                ->color('danger'),
            Stat::make('إجمالي الربح', number_format($summary['gross_profit']).' ج.م')
                ->description('الإيرادات - التكاليف المباشرة')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($grossProfitColor),
            Stat::make('مصروفات عمومية', number_format($summary['total_opex']).' ج.م')
                ->description(ExpenseClass::OPERATING_EXPENSES->getLabel())
                ->descriptionIcon('heroicon-m-presentation-chart-line')
                ->color('warning'),
            Stat::make('إجمالي المصروفات', number_format($summary['total_expenses']).' ج.م')
                ->description('مجموع التكاليف المباشرة والعمومية')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),
            Stat::make('صافي الدخل', number_format($summary['net_income']).' ج.م')
                ->description($summary['net_income'] >= 0 ? 'ربح' : 'خسارة')
                ->descriptionIcon($summary['net_income'] >= 0 ? 'heroicon-m-check-badge' : 'heroicon-m-exclamation-triangle')
                ->color($netIncomeColor),
        ];
    }
}
