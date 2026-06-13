<?php

namespace App\Filament\Pages\Reports;

use App\Enums\PaymentMethod;
use App\Filament\Components\Filters\DateRangeFilter;
use App\Filament\Pages\Reports\Widgets\ExpenseSummaryWidget;
use App\Filament\Resources\Trips\TripResource;
use App\Models\Expense;
use App\Services\PdfService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Pages\Page;
use Filament\Support\Enums\Size;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExpenseSummaryReport extends Page implements HasTable
{
    use HasReportFilters;
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-trending-down';

    protected static ?string $navigationLabel = 'تقرير المصروفات';

    protected static \UnitEnum|string|null $navigationGroup = 'التقارير المالية';

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.reports.expense-summary-report';

    protected static ?string $title = 'تقرير المصروفات';

    public function getBreadcrumbs(): array
    {
        return ['#' => static::$title];
    }

    public function getHeaderWidgets(): array
    {
        return [ExpenseSummaryWidget::class];
    }

    protected function getHeaderWidgetsData(): array
    {
        return [
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

    public function exportPdf(): StreamedResponse
    {
        $rows = $this->getFilteredSortedTableQuery()->get();
        $total = (int) $rows->sum('amount');
        $count = $rows->count();

        $pdf = app(PdfService::class)->generateReportPdf(
            title: static::$title,
            columns: ['#', 'التاريخ', 'التصنيف', 'البيان', 'الطريقة', 'المبلغ'],
            rows: $rows->map(fn (Expense $exp) => [
                $exp->id,
                $exp->paid_at?->format('Y-m-d') ?? '-',
                $exp->category ?? '-',
                $exp->description,
                $exp->payment_method?->getLabel() ?? '-',
                number_format($exp->amount),
            ])->toArray(),
            summaries: [
                ['', '', '', '', "عدد: {$count}", number_format($total)],
            ],
            filters: $this->buildFilterText(),
        );

        return response()->streamDownload(fn () => print ($pdf->output()), 'تقرير-المصروفات-'.now()->format('Y-m-d').'.pdf');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Expense::query()
                ->with('trip')
                ->latest('paid_at'))
            ->columns([
                TextColumn::make('paid_at')
                    ->label('التاريخ')
                    ->date('Y-m-d')
                    ->sortable(),
                TextColumn::make('category')
                    ->label('التصنيف')
                    ->badge()
                    ->searchable(),
                TextColumn::make('description')
                    ->label('البيان')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('trip.name')
                    ->label('الرحلة')
                    ->url(fn (Expense $record) => $record->trip ? TripResource::getUrl('edit', ['record' => $record->trip]) : null)
                    ->placeholder('—'),
                TextColumn::make('payment_method')
                    ->label('طريقة الدفع')
                    ->badge()
                    ->placeholder('—'),
                TextColumn::make('amount')
                    ->label('المبلغ')
                    ->money(config('app.currency'), locale: config('app.currency_locale'), decimalPlaces: 0)
                    ->sortable()
                    ->summarize(Sum::make()->money(config('app.currency'), locale: config('app.currency_locale'), decimalPlaces: 0)->label('الإجمالي')),
            ])
            ->defaultSort('paid_at', 'desc')
            ->filters([
                DateRangeFilter::make('date', 'paid_at'),
                SelectFilter::make('category')
                    ->label('التصنيف')
                    ->options(fn () => Expense::query()->whereNotNull('category')->distinct()->pluck('category', 'category')->toArray())
                    ->searchable(),
                SelectFilter::make('payment_method')
                    ->label('طريقة الدفع')
                    ->options(PaymentMethod::class),
            ]);
    }

    public function getExpenseSummary(): array
    {
        $dateFrom = $this->tableFilters['date']['date_from'] ?? null;
        $dateTo = $this->tableFilters['date']['date_to'] ?? null;

        return ExpenseSummaryWidget::computeSummary($dateFrom, $dateTo, null);
    }
}
