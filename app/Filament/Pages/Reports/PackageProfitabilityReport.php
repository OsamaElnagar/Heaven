<?php

namespace App\Filament\Pages\Reports;

use App\Enums\ExpenseStatus;
use App\Enums\PackageType;
use App\Filament\Pages\Reports\Widgets\PackageProfitabilitySummaryWidget;
use App\Filament\Resources\Packages\PackageResource;
use App\Models\Booking;
use App\Models\Package;
use App\Models\ReceiptVoucher;
use App\Models\RefundVoucher;
use App\Services\PdfService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Pages\Page;
use Filament\Support\Enums\Size;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PackageProfitabilityReport extends Page implements HasTable
{
    use HasReportFilters;
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-pie';

    protected static ?string $navigationLabel = 'ربحية الباقات';

    protected static \UnitEnum|string|null $navigationGroup = 'التقارير المالية';

    protected static ?int $navigationSort = 6;

    protected string $view = 'filament.pages.reports.package-profitability-report';

    protected static ?string $title = 'تقرير ربحية الباقات';

    public function getBreadcrumbs(): array
    {
        return ['#' => static::$title];
    }

    public function getHeaderWidgets(): array
    {
        return [PackageProfitabilitySummaryWidget::class];
    }

    protected function getHeaderWidgetsData(): array
    {
        return [
            'type' => $this->tableFilters['type']['value'] ?? null,
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
        $summary = $this->getProfitabilitySummary();

        $pdf = app(PdfService::class)->generateReportPdf(
            title: static::$title,
            columns: ['الباقة', 'النوع', 'عدد الحجوزات', 'إجمالي قيمة', 'المحصل', 'المستحق', 'نسبة التحصيل'],
            rows: $rows->map(fn (Package $package) => [
                $package->name,
                $package->type?->getLabel() ?? '-',
                (string) $package->bookings_count,
                number_format($package->total_revenue),
                number_format($package->collected),
                number_format($package->outstanding),
                $package->total_revenue > 0 ? number_format(($package->collected / $package->total_revenue) * 100, 1).' %' : '0 %',
            ])->toArray(),
            summaries: [
                ['', '', number_format($summary['total_bookings']), number_format($summary['total_revenue']), number_format($summary['total_collected']), number_format($summary['total_outstanding']), ''],
            ],
            filters: $this->buildFilterText(),
        );

        return response()->streamDownload(fn () => print ($pdf->output()), 'تقرير-ربحية-الباقات-'.now()->format('Y-m-d').'.pdf');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Package::query()
                    ->with(['bookings.receiptVouchers', 'bookings.refundVouchers'])
                    ->withCount('bookings')
                    ->addSelect([
                        'collected' => ReceiptVoucher::selectRaw('COALESCE(SUM(amount), 0)')
                            ->whereIn('booking_id', Booking::select('id')->whereColumn('package_id', 'packages.id'))
                            ->where('status', ExpenseStatus::POSTED->value),
                    ])
                    ->addSelect([
                        'refunded' => RefundVoucher::selectRaw('COALESCE(SUM(amount), 0)')
                            ->whereIn('booking_id', Booking::select('id')->whereColumn('package_id', 'packages.id'))
                            ->where('status', ExpenseStatus::POSTED->value),
                    ])
                    ->addSelect([
                        'total_revenue' => Booking::selectRaw('COALESCE(SUM(net_price), 0)')
                            ->whereColumn('package_id', 'packages.id'),
                    ])
            )
            ->columns([
                TextColumn::make('name')
                    ->label('الباقة')
                    ->url(fn (Package $record) => PackageResource::getUrl('edit', ['record' => $record]))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('النوع')
                    ->badge(),
                TextColumn::make('bookings_count')
                    ->label('عدد الحجوزات')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_revenue')
                    ->label('إجمالي القيمة')
                    ->money(config('app.currency'), locale: config('app.currency_locale'), decimalPlaces: 0),
                TextColumn::make('collected')
                    ->label('المحصل')
                    ->money(config('app.currency'), locale: config('app.currency_locale'), decimalPlaces: 0)
                    ->color('success'),
                TextColumn::make('outstanding')
                    ->label('المستحق')
                    ->money(config('app.currency'), locale: config('app.currency_locale'), decimalPlaces: 0)
                    ->color('warning')
                    ->getStateUsing(fn (Package $record) => max(0, (int) $record->total_revenue - (int) $record->collected + (int) $record->refunded)),
                TextColumn::make('collection_rate')
                    ->label('نسبة التحصيل')
                    ->getStateUsing(function (Package $record) {
                        $rev = (int) $record->total_revenue;

                        return $rev > 0 ? number_format(((int) $record->collected / $rev) * 100, 1).' %' : '0 %';
                    })
                    ->color('info'),
            ])
            ->defaultSort('total_revenue', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->label('نوع الباقة')
                    ->options(PackageType::class),
            ]);
    }

    public function getProfitabilitySummary(): array
    {
        $type = $this->tableFilters['type']['value'] ?? null;

        return PackageProfitabilitySummaryWidget::computeSummary($type);
    }
}
