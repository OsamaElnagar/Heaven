<?php

namespace App\Filament\Pages\Reports;

use App\Filament\Components\Filters\DateRangeFilter;
use App\Filament\Pages\Reports\Widgets\CashFlowSummaryWidget;
use App\Models\FiscalYear;
use App\Models\PaymentVoucher;
use App\Models\ReceiptVoucher;
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
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CashFlowStatement extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationLabel = 'قائمة التدفقات النقدية';

    protected static \UnitEnum|string|null $navigationGroup = 'التقارير المالية';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.reports.cash-flow-statement';

    protected static ?string $title = 'قائمة التدفقات النقدية';

    public function getBreadcrumbs(): array
    {
        return ['#' => static::$title];
    }

    public function getHeaderWidgets(): array
    {
        return [CashFlowSummaryWidget::class];
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

    protected function voucherBaseQuery(): Builder
    {
        $dateFrom = $this->tableFilters['date']['date_from'] ?? null;
        $dateTo = $this->tableFilters['date']['date_to'] ?? null;

        $receipts = ReceiptVoucher::query()
            ->where('status', 'posted')
            ->when($dateFrom, fn ($q) => $q->whereDate('voucher_date', '>=', Carbon::parse($dateFrom)))
            ->when($dateTo, fn ($q) => $q->whereDate('voucher_date', '<=', Carbon::parse($dateTo)))
            ->selectRaw("
                voucher_date as date,
                'وارد نقدي' as direction,
                'سند قبض' as source_type,
                number as source_number,
                description,
                amount,
                id,
                'receipt' as voucher_type
            ");

        $payments = PaymentVoucher::query()
            ->where('status', 'posted')
            ->when($dateFrom, fn ($q) => $q->whereDate('voucher_date', '>=', Carbon::parse($dateFrom)))
            ->when($dateTo, fn ($q) => $q->whereDate('voucher_date', '<=', Carbon::parse($dateTo)))
            ->selectRaw("
                voucher_date as date,
                'صادر نقدي' as direction,
                'سند صرف' as source_type,
                number as source_number,
                description,
                amount,
                id,
                'payment' as voucher_type
            ");

        $union = $receipts->unionAll($payments);

        $model = new class extends Model
        {
            protected $table = 'cash_flows';
        };

        return $model->newQuery()->fromSub($union, 'cash_flows');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->voucherBaseQuery())
            ->defaultSort('date', 'desc')
            ->columns([
                TextColumn::make('date')
                    ->label('التاريخ')
                    ->date('Y-m-d'),
                TextColumn::make('source_type')
                    ->label('النوع')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'سند قبض' ? 'success' : 'danger'),
                TextColumn::make('source_number')
                    ->label('الرقم')
                    ->searchable(),
                TextColumn::make('description')
                    ->label('البيان')
                    ->wrap(),
                TextColumn::make('direction')
                    ->label('الاتجاه')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'وارد نقدي' ? 'success' : 'danger'),
                TextColumn::make('cf_category')
                    ->label('تصنيف التدفق')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'تشغيلي' => 'info',
                        'تمويلي' => 'success',
                        default => 'gray',
                    })
                    ->getStateUsing(fn (): string => 'تشغيلي'),
                TextColumn::make('amount')
                    ->label('المبلغ')
                    ->money('EGP', locale: 'en', decimalPlaces: 0)
                    ->color(fn ($record): string => $record->direction === 'وارد نقدي' ? 'success' : 'danger'),
            ])
            ->filters([
                DateRangeFilter::make('date', 'date'),
                SelectFilter::make('fiscal_year_id')
                    ->label('السنة المالية')
                    ->options(fn () => FiscalYear::pluck('name', 'id')->toArray())
                    ->default(fn () => FiscalYear::where('status', 'open')->value('id'))
                    ->query(fn (Builder $query) => $query),
            ]);
    }

    public function getCashFlowSummary(): array
    {
        $dateFrom = $this->tableFilters['date']['date_from'] ?? null;
        $dateTo = $this->tableFilters['date']['date_to'] ?? null;

        return CashFlowSummaryWidget::computeSummary($dateFrom, $dateTo);
    }

    public function exportPdf(): StreamedResponse
    {
        $rows = $this->getFilteredSortedTableQuery()->get();
        $rows->each(fn ($r) => $r->cf_category = 'تشغيلي');
        $summary = $this->getCashFlowSummary();

        $pdf = app(PdfService::class)->generateReportPdf(
            title: static::$title,
            columns: ['التاريخ', 'النوع', 'الرقم', 'البيان', 'الاتجاه', 'التصنيف', 'المبلغ'],
            rows: $rows->map(fn ($row) => [
                $row->date instanceof Carbon ? $row->date->format('Y-m-d') : $row->date,
                $row->source_type,
                $row->source_number,
                $row->description,
                $row->direction,
                $row->cf_category,
                number_format($row->amount),
            ])->toArray(),
            summaries: [
                ['', '', '', 'الأنشطة التشغيلية - وارد', '', number_format($summary['operating_in'])],
                ['', '', '', 'الأنشطة التشغيلية - صادر', '', number_format($summary['operating_out'])],
                ['', '', '', 'صافي الأنشطة التشغيلية', '', number_format($summary['operating_net'])],
                ['', '', '', 'صافي التدفق النقدي', '', number_format($summary['total_net'])],
            ],
        );

        return response()->streamDownload(fn () => print ($pdf->output()), 'التدفقات-النقدية-'.now()->format('Y-m-d').'.pdf');
    }
}
