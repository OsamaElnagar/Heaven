<?php

namespace App\Filament\Pages\Reports;

use App\Enums\AccountClass;
use App\Filament\Components\Filters\DateRangeFilter;
use App\Filament\Pages\Reports\Widgets\BalanceSheetSummaryWidget;
use App\Models\Account;
use App\Models\AccountOpeningBalance;
use App\Models\FiscalYear;
use App\Models\JournalLine;
use App\Services\PdfService;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Pages\Page;
use Filament\Support\Enums\Size;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BalanceSheet extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-scale';

    protected static ?string $navigationLabel = 'الميزانية العمومية';

    protected static \UnitEnum|string|null $navigationGroup = 'التقارير المالية';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.reports.balance-sheet';

    protected static ?string $title = 'الميزانية العمومية';

    public function getBreadcrumbs(): array
    {
        return ['#' => static::$title];
    }

    public function getHeaderWidgets(): array
    {
        return [BalanceSheetSummaryWidget::class];
    }

    protected function getHeaderWidgetsData(): array
    {
        return [
            'fiscalYearId' => $this->tableFilters['fiscal_year_id']['value']
                ?? FiscalYear::where('status', 'open')->value('id'),
            'dateFrom' => $this->tableFilters['date']['date_from'] ?? null,
            'dateTo' => $this->tableFilters['date']['date_to'] ?? null,
        ];
    }

    public function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('exportPdf')
                    ->label('تصدير PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('info')
                    ->action(fn () => $this->exportPdf()),
            ])
                ->label('تصدير')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size(Size::Small)
                ->color('primary')
                ->button(),
        ];
    }

    public function table(Table $table): Table
    {
        $fiscalYearId = $this->tableFilters['fiscal_year_id']['value']
            ?? FiscalYear::where('status', 'open')->value('id');
        $dateFrom = $this->tableFilters['date']['date_from'] ?? null;
        $dateTo = $this->tableFilters['date']['date_to'] ?? null;

        $periodSub = JournalLine::query()
            ->selectRaw('account_id, SUM(debit_amount) as period_debit, SUM(credit_amount) as period_credit')
            ->whereHas('journalEntry', fn ($q) => $q
                ->where('status', 'posted')
                ->whereNull('deleted_at')
                ->when($fiscalYearId, fn ($q) => $q->where('fiscal_year_id', $fiscalYearId))
                ->when($dateFrom, fn ($q) => $q->whereDate('entry_date', '>=', Carbon::parse($dateFrom)))
                ->when($dateTo, fn ($q) => $q->whereDate('entry_date', '<=', Carbon::parse($dateTo)))
            )
            ->groupBy('account_id');

        return $table
            ->query(
                Account::query()
                    ->whereIn('class', [AccountClass::ASSETS, AccountClass::LIABILITIES, AccountClass::EQUITY])
                    ->where('is_active', true)
                    ->where('type', 'detail')
                    ->leftJoinSub($periodSub, 'period', fn ($join) => $join->on('accounts.id', '=', 'period.account_id'))
                    ->addSelect([
                        'opening_debit' => AccountOpeningBalance::select('debit_amount')
                            ->whereColumn('account_id', 'accounts.id')
                            ->when($fiscalYearId, fn ($q) => $q->where('fiscal_year_id', $fiscalYearId))
                            ->limit(1),
                        'opening_credit' => AccountOpeningBalance::select('credit_amount')
                            ->whereColumn('account_id', 'accounts.id')
                            ->when($fiscalYearId, fn ($q) => $q->where('fiscal_year_id', $fiscalYearId))
                            ->limit(1),
                    ])
                    ->select('accounts.*')
                    ->selectRaw('
                        COALESCE(period.period_debit, 0) as total_debit,
                        COALESCE(period.period_credit, 0) as total_credit
                    ')
                    ->whereNull('accounts.deleted_at')
            )
            ->columns([
                TextColumn::make('code')
                    ->label('كود الحساب')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('اسم الحساب')
                    ->searchable(),
                TextColumn::make('class')
                    ->label('التصنيف'),
                TextColumn::make('total_debit')
                    ->label('مدين')
                    ->money('EGP', locale: 'en', decimalPlaces: 0)
                    ->color('danger'),
                TextColumn::make('total_credit')
                    ->label('دائن')
                    ->money('EGP', locale: 'en', decimalPlaces: 0)
                    ->color('success'),
                TextColumn::make('net_balance')
                    ->label('الرصيد')
                    ->money('EGP', locale: 'en', decimalPlaces: 0)
                    ->getStateUsing(function (Account $record): int {
                        $totalDebit = (int) $record->total_debit + (int) ($record->opening_debit ?? 0);
                        $totalCredit = (int) $record->total_credit + (int) ($record->opening_credit ?? 0);

                        if ($record->class === AccountClass::ASSETS) {
                            return $totalDebit - $totalCredit;
                        }

                        return $totalCredit - $totalDebit;
                    })
                    ->color(fn ($state) => $state >= 0 ? 'success' : 'danger'),
            ])
            ->defaultSort('code')
            ->filters([
                SelectFilter::make('class')
                    ->label('التصنيف')
                    ->options([
                        AccountClass::ASSETS->value => AccountClass::ASSETS->getLabel(),
                        AccountClass::LIABILITIES->value => AccountClass::LIABILITIES->getLabel(),
                        AccountClass::EQUITY->value => AccountClass::EQUITY->getLabel(),
                    ]),
                DateRangeFilter::make('date', 'journal_entries.entry_date'),
                SelectFilter::make('fiscal_year_id')
                    ->label('السنة المالية')
                    ->options(fn () => FiscalYear::pluck('name', 'id')->toArray())
                    ->default(fn () => FiscalYear::where('status', 'open')->value('id'))
                    ->query(fn (Builder $query) => $query),
            ]);
    }

    public function getBalanceSheetSummary(): array
    {
        $fiscalYearId = $this->tableFilters['fiscal_year_id']['value']
            ?? FiscalYear::where('status', 'open')->value('id');
        $dateFrom = $this->tableFilters['date']['date_from'] ?? null;
        $dateTo = $this->tableFilters['date']['date_to'] ?? null;

        return BalanceSheetSummaryWidget::computeSummary($fiscalYearId, $dateFrom, $dateTo);
    }

    public function exportPdf(): StreamedResponse
    {
        $rows = $this->getFilteredSortedTableQuery()->get();
        $summary = $this->getBalanceSheetSummary();

        $pdf = app(PdfService::class)->generateReportPdf(
            title: static::$title,
            columns: ['كود الحساب', 'اسم الحساب', 'التصنيف', 'مدين', 'دائن', 'الرصيد'],
            rows: $rows->map(fn (Account $acc) => [
                $acc->code,
                $acc->name,
                $acc->class->getLabel(),
                number_format((int) $acc->total_debit + (int) ($acc->opening_debit ?? 0)),
                number_format((int) $acc->total_credit + (int) ($acc->opening_credit ?? 0)),
                number_format($acc->class === AccountClass::ASSETS
                    ? ((int) $acc->total_debit + (int) ($acc->opening_debit ?? 0)) - ((int) $acc->total_credit + (int) ($acc->opening_credit ?? 0))
                    : ((int) $acc->total_credit + (int) ($acc->opening_credit ?? 0)) - ((int) $acc->total_debit + (int) ($acc->opening_debit ?? 0))),
            ])->toArray(),
            summaries: [
                ['', 'أصول', '', '', number_format($summary['total_assets'])],
                ['', 'خصوم', '', '', number_format($summary['total_liabilities'])],
                ['', 'حقوق ملكية (بدون صافي الدخل)', '', '', number_format($summary['total_equity'])],
                ['', 'صافي الدخل (من قائمة الدخل)', '', '', number_format($summary['net_income'])],
                ['', 'إجمالي الخصوم + حقوق الملكية', '', '', number_format($summary['total_liabilities_equity'])],
                ['', 'الفرق', '', '', number_format($summary['difference'])],
            ],
        );

        return response()->streamDownload(fn () => print ($pdf->output()), 'الميزانية-العمومية-'.now()->format('Y-m-d').'.pdf');
    }
}
