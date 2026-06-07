<?php

namespace App\Filament\Resources\JournalEntries\Pages;

use App\Filament\Resources\JournalEntries\JournalEntryResource;
use App\Models\Account;
use App\Models\AccountOpeningBalance;
use App\Models\FiscalYear;
use App\Models\JournalLine;
use Carbon\Carbon;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TrialBalance extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = JournalEntryResource::class;

    protected string $view = 'filament.pages.statement';

    protected static ?string $title = 'ميزان المراجعة';

    public function getBreadcrumbs(): array
    {
        return [
            JournalEntryResource::getUrl('index') => JournalEntryResource::getBreadcrumb(),
            '#' => static::$title,
        ];
    }

    public function table(Table $table): Table
    {
        $fiscalYearId = $this->tableFilters['fiscal_year_id']['value']
            ?? FiscalYear::where('status', 'open')->value('id');
        $dateFrom = $this->tableFilters['date']['date_from'] ?? null;
        $dateTo = $this->tableFilters['date']['date_to'] ?? null;

        $periodSub = JournalLine::query()
            ->selectRaw('
                account_id,
                SUM(debit_amount) as period_debit,
                SUM(credit_amount) as period_credit
            ')
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
                    ->where('is_active', true)
                    ->leftJoinSub($periodSub, 'period', fn ($join) => $join->on('accounts.id', '=', 'period.account_id'))
                    ->addSelect([
                        'opening_debit' => AccountOpeningBalance::select('debit_amount')
                            ->whereColumn('account_id', 'accounts.id')
                            ->when($fiscalYearId, fn ($q) => $q->where('fiscal_year_id', $fiscalYearId), fn ($q) => $q->whereNull('id'))
                            ->limit(1),
                        'opening_credit' => AccountOpeningBalance::select('credit_amount')
                            ->whereColumn('account_id', 'accounts.id')
                            ->when($fiscalYearId, fn ($q) => $q->where('fiscal_year_id', $fiscalYearId), fn ($q) => $q->whereNull('id'))
                            ->limit(1),
                    ])
                    ->select('accounts.*')
                    ->selectRaw('
                        accounts.code as account_code,
                        accounts.name as account_name,
                        COALESCE(period.period_debit, 0) as total_debit,
                        COALESCE(period.period_credit, 0) as total_credit
                    ')
                    ->whereNull('accounts.deleted_at')
            )
            ->columns([
                TextColumn::make('account_code')
                    ->label('كود الحساب')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('account_name')
                    ->label('اسم الحساب')
                    ->searchable(),
                TextColumn::make('opening_balance')
                    ->label('الرصيد الافتتاحي')
                    ->getStateUsing(fn ($record): int => $record->normal_balance === 'debit'
                        ? (int) ($record->opening_debit ?? 0) - (int) ($record->opening_credit ?? 0)
                        : (int) ($record->opening_credit ?? 0) - (int) ($record->opening_debit ?? 0)),
                TextColumn::make('total_debit')
                    ->label('مدين')
                    ->numeric()
                    ->color('danger')
                    ->summarize(Sum::make()->label('إجمالي المدين')),
                TextColumn::make('total_credit')
                    ->label('دائن')
                    ->numeric()
                    ->color('success')
                    ->summarize(Sum::make()->label('إجمالي الدائن')),
                TextColumn::make('closing_balance')
                    ->label('الرصيد الختامي')
                    ->getStateUsing(fn ($record): int => $record->normal_balance === 'debit'
                        ? ((int) ($record->opening_debit ?? 0) + (int) $record->total_debit)
                          - ((int) ($record->opening_credit ?? 0) + (int) $record->total_credit)
                        : ((int) ($record->opening_credit ?? 0) + (int) $record->total_credit)
                          - ((int) ($record->opening_debit ?? 0) + (int) $record->total_debit))
                    ->color(fn ($record) => (($record->normal_balance === 'debit'
                        ? ((int) ($record->opening_debit ?? 0) + (int) $record->total_debit)
                          - ((int) ($record->opening_credit ?? 0) + (int) $record->total_credit)
                        : ((int) ($record->opening_credit ?? 0) + (int) $record->total_credit)
                          - ((int) ($record->opening_debit ?? 0) + (int) $record->total_debit))) >= 0 ? 'danger' : 'success'),
            ])
            ->defaultSort('accounts.code')
            ->filters([
                SelectFilter::make('fiscal_year_id')
                    ->label('السنة المالية')
                    ->options(fn () => FiscalYear::pluck('name', 'id')->toArray())
                    ->default(fn () => FiscalYear::where('status', 'open')->value('id')),
            ]);
    }
}
