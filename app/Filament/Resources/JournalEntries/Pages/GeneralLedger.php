<?php

namespace App\Filament\Resources\JournalEntries\Pages;

use App\Filament\Resources\JournalEntries\JournalEntryResource;
use App\Models\Account;
use App\Models\FiscalYear;
use App\Models\JournalLine;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GeneralLedger extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = JournalEntryResource::class;

    protected string $view = 'filament.pages.statement';

    protected static ?string $title = 'الأستاذ العام';

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

        return $table
            ->query(
                JournalLine::query()
                    ->with(['journalEntry', 'account'])
                    ->whereHas('journalEntry', fn ($q) => $q
                        ->where('status', 'posted')
                        ->when($fiscalYearId, fn ($q) => $q->where('fiscal_year_id', $fiscalYearId))
                    )
            )
            ->columns([
                TextColumn::make('journalEntry.entry_date')
                    ->label('التاريخ')
                    ->date('Y-m-d')
                    ->sortable(),
                TextColumn::make('journalEntry.number')
                    ->label('رقم القيد')
                    ->searchable(),
                TextColumn::make('account.code')
                    ->label('كود الحساب')
                    ->searchable(),
                TextColumn::make('account.name')
                    ->label('اسم الحساب')
                    ->searchable(),
                TextColumn::make('description')
                    ->label('البيان')
                    ->wrap()
                    ->getStateUsing(fn (JournalLine $record) => $record->description ?: $record->journalEntry?->description),
                TextColumn::make('debit_amount')
                    ->label('مدين')
                    ->numeric()
                    ->color('danger')
                    ->summarize(Sum::make()->label('إجمالي المدين')),
                TextColumn::make('credit_amount')
                    ->label('دائن')
                    ->numeric()
                    ->color('success')
                    ->summarize(Sum::make()->label('إجمالي الدائن')),
            ])
            ->defaultSort('journal_entries.entry_date', 'asc')
            ->filters([
                SelectFilter::make('fiscal_year_id')
                    ->label('السنة المالية')
                    ->options(fn () => FiscalYear::pluck('name', 'id')->toArray())
                    ->default(fn () => FiscalYear::where('status', 'open')->value('id')),
                SelectFilter::make('account_id')
                    ->label('الحساب')
                    ->options(fn () => Account::query()->pluck('name', 'id')->toArray())
                    ->searchable(),
                Filter::make('date')
                    ->label('الفترة')
                    ->schema([
                        DatePicker::make('date_from')
                            ->label('من تاريخ')
                            ->displayFormat('Y-m-d')
                            ->native(false),
                        DatePicker::make('date_to')
                            ->label('إلى تاريخ')
                            ->displayFormat('Y-m-d')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereHas(
                                    'journalEntry',
                                    fn ($q) => $q->whereDate('entry_date', '>=', Carbon::parse($date))
                                ),
                            )
                            ->when(
                                $data['date_to'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereHas(
                                    'journalEntry',
                                    fn ($q) => $q->whereDate('entry_date', '<=', Carbon::parse($date))
                                ),
                            );
                    }),
            ]);
    }
}
