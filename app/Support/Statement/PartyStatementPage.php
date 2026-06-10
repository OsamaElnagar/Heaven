<?php

namespace App\Support\Statement;

use App\Actions\Statements\BuildJournalStatementRowsAction;
use App\Enums\JournalEntryStatus;
use App\Models\JournalLine;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

abstract class PartyStatementPage extends Page implements HasTable
{
    use InteractsWithRecord;
    use InteractsWithTable;

    protected ?array $balanceMap = null;

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    protected string $view = 'filament.pages.party-statement';

    abstract protected function statementAccountId(): ?int;

    abstract protected function statementEntityLabel(): string;

    public function content(Schema $schema): Schema
    {
        return $schema;
    }

    public function table(Table $table): Table
    {
        $accountId = $this->statementAccountId();

        return $table
            ->query(
                JournalLine::query()
                    ->whereHas('journalEntry', fn ($q) => $q->where('status', JournalEntryStatus::POSTED))
                    ->when($accountId, fn ($q) => $q->where('account_id', $accountId))
                    ->with(['journalEntry'])
                    ->orderBy('id'),
            )
            ->columns([
                TextColumn::make('journalEntry.entry_date')
                    ->label('التاريخ')
                    ->date('Y-m-d')
                    ->sortable(),
                TextColumn::make('journalEntry.number')
                    ->label('رقم القيد')
                    ->searchable(),
                TextColumn::make('description')
                    ->label('البيان')
                    ->wrap()
                    ->searchable(),
                TextColumn::make('journalEntry.source_type')
                    ->label('المصدر')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'receipt_voucher' => 'سند قبض',
                        'payment_voucher' => 'سند صرف',
                        'refund_voucher' => 'سند استرداد',
                        'opening_balance' => 'رصيد افتتاحي',
                        'journal_entry' => 'قيد يومية',
                        'manual' => 'قيد يدوي',
                        default => $state ?? '-',
                    }),
                TextColumn::make('debit_amount')
                    ->label('مدين')
                    ->numeric()
                    ->state(fn ($record) => $record instanceof JournalLine ? (int) $record->debit_amount : 0)
                    ->summarize(Sum::make()),
                TextColumn::make('credit_amount')
                    ->label('دائن')
                    ->numeric()
                    ->state(fn ($record) => $record instanceof JournalLine ? (int) $record->credit_amount : 0)
                    ->summarize(Sum::make()),
                TextColumn::make('balance')
                    ->label('الرصيد')
                    ->numeric()
                    ->state(function ($record) {
                        if (! $record instanceof JournalLine) {
                            return 0;
                        }

                        return (int) ($this->getBalanceMap()[$record->id]['balance'] ?? 0);
                    }),
            ])
            ->filters([
                Filter::make('date')
                    ->form([
                        DatePicker::make('from')->label('من تاريخ'),
                        DatePicker::make('to')->label('إلى تاريخ'),
                    ])
                    ->query(function ($query, array $data) use ($accountId) {
                        if (! $accountId) {
                            return $query;
                        }
                        if (! empty($data['from'])) {
                            $query->whereHas('journalEntry', fn ($q) => $q->whereDate('entry_date', '>=', $data['from']));
                        }
                        if (! empty($data['to'])) {
                            $query->whereHas('journalEntry', fn ($q) => $q->whereDate('entry_date', '<=', $data['to']));
                        }

                        return $query;
                    }),
            ])
            ->paginated(false)
            ->headerActions([]);
    }

    protected function getBalanceMap(): array
    {
        if ($this->balanceMap !== null) {
            return $this->balanceMap;
        }

        $accountId = $this->statementAccountId();
        if (! $accountId) {
            $this->balanceMap = [];

            return $this->balanceMap;
        }

        $filters = $this->resolveDateFilters();
        $rows = app(BuildJournalStatementRowsAction::class)
            ->execute($accountId, $filters['from'], $filters['to']);

        $this->balanceMap = $rows->keyBy('line_id')->toArray();

        return $this->balanceMap;
    }

    public function getViewData(): array
    {
        $accountId = $this->statementAccountId();
        if (! $accountId) {
            return ['rows' => collect(), 'openingBalance' => 0, 'entityName' => $this->statementEntityLabel(), 'accountId' => null];
        }

        $filters = $this->resolveDateFilters();

        $rows = app(BuildJournalStatementRowsAction::class)
            ->execute($accountId, $filters['from'], $filters['to']);

        $opening = app(BuildJournalStatementRowsAction::class)->getBalanceBefore(
            $accountId,
            $filters['from'],
        );

        return [
            'rows' => $rows,
            'openingBalance' => $opening,
            'entityName' => $this->statementEntityLabel(),
            'accountId' => $accountId,
            'from' => $filters['from']?->toDateString(),
            'to' => $filters['to']?->toDateString(),
        ];
    }

    /**
     * @return array{from: ?Carbon, to: ?Carbon}
     */
    protected function resolveDateFilters(): array
    {
        $tableFilters = $this->tableFilters ?? [];
        $from = $tableFilters['date']['from'] ?? null;
        $to = $tableFilters['date']['to'] ?? null;

        return [
            'from' => $from ? Carbon::parse($from) : null,
            'to' => $to ? Carbon::parse($to) : null,
        ];
    }
}
