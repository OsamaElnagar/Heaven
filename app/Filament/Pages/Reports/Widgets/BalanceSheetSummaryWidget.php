<?php

namespace App\Filament\Pages\Reports\Widgets;

use App\Enums\AccountClass;
use App\Models\Account;
use App\Models\AccountOpeningBalance;
use App\Models\FiscalYear;
use App\Models\JournalLine;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class BalanceSheetSummaryWidget extends BaseWidget
{
    protected ?string $pollingInterval = null;

    public ?int $fiscalYearId = null;

    public ?string $dateFrom = null;

    public ?string $dateTo = null;

    public function mount(): void
    {
        $this->fiscalYearId ??= FiscalYear::where('status', 'open')->value('id');
    }

    public static function canView(): bool
    {
        return true;
    }

    public static function computeSummary(?int $fiscalYearId, ?string $dateFrom, ?string $dateTo): array
    {
        $periodSub = JournalLine::query()
            ->selectRaw('account_id, SUM(debit_amount) as period_debit, SUM(credit_amount) as period_credit')
            ->whereHas('journalEntry', fn ($q) => $q
                ->where('status', 'posted')
                ->whereNull('deleted_at')
                ->when($fiscalYearId, fn ($q) => $q->where('fiscal_year_id', $fiscalYearId))
                ->when($dateFrom, fn ($q) => $q->whereDate('entry_date', '>=', $dateFrom))
                ->when($dateTo, fn ($q) => $q->whereDate('entry_date', '<=', $dateTo))
            )
            ->groupBy('account_id');

        $netIncomeSub = JournalLine::query()
            ->selectRaw('
                COALESCE(SUM(CASE WHEN rev_acc.id IS NOT NULL THEN journal_lines.credit_amount ELSE 0 END), 0)
                - COALESCE(SUM(CASE WHEN exp_acc.id IS NOT NULL THEN journal_lines.debit_amount ELSE 0 END), 0) as net
            ')
            ->join('journal_entries as je_sub', 'journal_lines.journal_entry_id', '=', 'je_sub.id')
            ->leftJoin('accounts as rev_acc', fn ($join) => $join
                ->on('journal_lines.account_id', '=', 'rev_acc.id')
                ->where('rev_acc.class', 'revenue')
            )
            ->leftJoin('accounts as exp_acc', fn ($join) => $join
                ->on('journal_lines.account_id', '=', 'exp_acc.id')
                ->where('exp_acc.class', 'expenses')
            )
            ->where('je_sub.status', 'posted')
            ->whereNull('je_sub.deleted_at')
            ->when($fiscalYearId, fn ($q) => $q->where('je_sub.fiscal_year_id', $fiscalYearId))
            ->when($dateFrom, fn ($q) => $q->whereDate('je_sub.entry_date', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->whereDate('je_sub.entry_date', '<=', $dateTo));

        $records = Account::query()
            ->whereIn('class', [AccountClass::ASSETS, AccountClass::LIABILITIES, AccountClass::EQUITY])
            ->where('is_active', true)
            ->where('type', 'detail')
            ->leftJoinSub($periodSub, 'period', fn ($join) => $join->on('accounts.id', '=', 'period.account_id'))
            ->addSelect([
                'accounts.*',
                'opening_debit' => AccountOpeningBalance::select('debit_amount')
                    ->whereColumn('account_id', 'accounts.id')
                    ->when($fiscalYearId, fn ($q) => $q->where('fiscal_year_id', $fiscalYearId))
                    ->limit(1),
                'opening_credit' => AccountOpeningBalance::select('credit_amount')
                    ->whereColumn('account_id', 'accounts.id')
                    ->when($fiscalYearId, fn ($q) => $q->where('fiscal_year_id', $fiscalYearId))
                    ->limit(1),
            ])
            ->selectRaw('
                COALESCE(period.period_debit, 0) as total_debit,
                COALESCE(period.period_credit, 0) as total_credit
            ')
            ->whereNull('accounts.deleted_at')
            ->get();

        $netIncome = (int) DB::query()->fromSub($netIncomeSub, 't')->value('net');

        $totalAssets = $records->where('class', AccountClass::ASSETS->value)
            ->sum(fn ($r) => ((int) ($r->opening_debit ?? 0) + (int) $r->total_debit) - ((int) ($r->opening_credit ?? 0) + (int) $r->total_credit));

        $totalLiabilities = $records->where('class', AccountClass::LIABILITIES->value)
            ->sum(fn ($r) => ((int) ($r->opening_credit ?? 0) + (int) $r->total_credit) - ((int) ($r->opening_debit ?? 0) + (int) $r->total_debit));

        $totalEquity = $records->where('class', AccountClass::EQUITY->value)
            ->sum(fn ($r) => ((int) ($r->opening_credit ?? 0) + (int) $r->total_credit) - ((int) ($r->opening_debit ?? 0) + (int) $r->total_debit));

        $totalLiabilitiesEquity = $totalLiabilities + $totalEquity + $netIncome;

        return [
            'total_assets' => $totalAssets,
            'total_liabilities' => $totalLiabilities,
            'total_equity' => $totalEquity,
            'net_income' => $netIncome,
            'total_liabilities_equity' => $totalLiabilitiesEquity,
            'difference' => $totalAssets - $totalLiabilitiesEquity,
            'is_balanced' => $totalAssets === $totalLiabilitiesEquity,
        ];
    }

    public function getSummary(): array
    {
        return static::computeSummary($this->fiscalYearId, $this->dateFrom, $this->dateTo);
    }

    protected function getStats(): array
    {
        $summary = $this->getSummary();
        $isBalanced = $summary['is_balanced'];

        return [
            Stat::make('إجمالي الأصول', number_format($summary['total_assets']).' ج.م')
                ->description('مجموع أرصدة حسابات الأصول')
                ->descriptionIcon('heroicon-m-building-library')
                ->color('info'),
            Stat::make('إجمالي الخصوم', number_format($summary['total_liabilities']).' ج.م')
                ->description('مجموع أرصدة حسابات الالتزامات')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('warning'),
            Stat::make('حقوق الملكية (شامل صافي الدخل)', number_format($summary['total_equity'] + $summary['net_income']).' ج.م')
                ->description('حقوق ملكية '.number_format($summary['total_equity']).' + صافي دخل '.number_format($summary['net_income']))
                ->descriptionIcon('heroicon-m-user-group')
                ->color('purple'),
            Stat::make($isBalanced ? 'الميزانية متوازنة ✓' : 'الميزانية غير متوازنة ✗', number_format($summary['difference']).' ج.م')
                ->description($isBalanced ? 'الأصول = الخصوم + حقوق الملكية' : 'فرق: '.number_format(abs($summary['difference'])))
                ->descriptionIcon($isBalanced ? 'heroicon-m-check-circle' : 'heroicon-m-exclamation-triangle')
                ->color($isBalanced ? 'success' : 'danger'),
        ];
    }
}
