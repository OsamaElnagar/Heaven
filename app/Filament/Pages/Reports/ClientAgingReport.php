<?php

namespace App\Filament\Pages\Reports;

use App\Enums\BookingStatus;
use App\Filament\Pages\Reports\Widgets\ClientAgingSummaryWidget;
use App\Filament\Resources\Clients\ClientResource;
use App\Models\Booking;
use App\Services\PdfService;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Support\Enums\Size;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClientAgingReport extends Page implements HasTable
{
    use HasReportFilters;
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'أعمار ديون العملاء';

    protected static \UnitEnum|string|null $navigationGroup = 'التقارير المالية';

    protected static ?int $navigationSort = 7;

    protected string $view = 'filament.pages.reports.aging-report';

    protected static ?string $title = 'تقرير أعمار ديون العملاء';

    public function getBreadcrumbs(): array
    {
        return ['#' => static::$title];
    }

    public function getHeaderWidgets(): array
    {
        return [ClientAgingSummaryWidget::class];
    }

    protected function getHeaderWidgetsData(): array
    {
        return [
            'asOfDate' => $this->tableFilters['report_date']['as_of_date'] ?? now()->format('Y-m-d'),
            'clientId' => $this->tableFilters['client_id']['value'] ?? null,
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
        $reportDate = $this->getReportDate();
        $summary = $this->getAgingSummary();

        $pdf = app(PdfService::class)->generateReportPdf(
            title: static::$title.' حتى '.$reportDate->format('Y-m-d'),
            columns: ['العميل', 'رقم الحجز', 'تاريخ الحجز', 'تاريخ الاستحقاق', 'قيمة الحجز', 'المدفوع', 'المستحق', 'عدد الأيام', 'الفئة'],
            rows: $rows->map(function (Booking $booking) {
                $dueDate = $booking->due_date;
                $agingDays = $this->getAgingDays($booking);

                return [
                    $booking->client?->name ?? '-',
                    $booking->reference,
                    $booking->created_at?->format('Y-m-d') ?? '-',
                    $dueDate?->format('Y-m-d') ?? '-',
                    number_format($booking->net_price),
                    number_format($booking->paid_amount),
                    number_format(max(0, (int) $booking->net_price - (int) $booking->paid_amount)),
                    (string) $agingDays,
                    $agingDays <= 30 ? '0-30 يوم' : ($agingDays <= 60 ? '30-60 يوم' : ($agingDays <= 90 ? '60-90 يوم' : 'أكثر من 90 يوم')),
                ];
            })->toArray(),
            summaries: [
                ['', '', '', '', number_format($rows->sum('net_price')), number_format($rows->sum('paid_amount')), number_format($summary['total_outstanding']), '', ''],
            ],
            filters: $this->buildFilterText(),
        );

        return response()->streamDownload(fn () => print ($pdf->output()), 'تقرير-أعمار-العملاء-'.now()->format('Y-m-d').'.pdf');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Booking::query()
                    ->with('client')
                    ->whereIn('status', [BookingStatus::PENDING->value, BookingStatus::CONFIRMED->value])
                    ->whereColumn('net_price', '>', 'paid_amount')
            )
            ->columns([
                TextColumn::make('client.name')
                    ->label('العميل')
                    ->url(fn (Booking $record) => $record->client ? ClientResource::getUrl('edit', ['record' => $record->client]) : null)
                    ->searchable(),
                TextColumn::make('reference')
                    ->label('رقم الحجز')
                    ->copyable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('تاريخ الحجز')
                    ->date('Y-m-d'),
                TextColumn::make('due_date')
                    ->label('تاريخ الاستحقاق')
                    ->date('Y-m-d'),
                TextColumn::make('net_price')
                    ->label('قيمة الحجز')
                    ->money(config('app.currency'), locale: config('app.currency_locale'), decimalPlaces: 0),
                TextColumn::make('paid_amount')
                    ->label('المدفوع')
                    ->money(config('app.currency'), locale: config('app.currency_locale'), decimalPlaces: 0),
                TextColumn::make('outstanding')
                    ->label('المستحق')
                    ->money(config('app.currency'), locale: config('app.currency_locale'), decimalPlaces: 0)
                    ->getStateUsing(fn (Booking $record): int => max(0, (int) $record->net_price - (int) $record->paid_amount)),
                TextColumn::make('aging_days')
                    ->label('عدد الأيام')
                    ->getStateUsing(fn (Booking $record): int => $this->getAgingDays($record)),
                TextColumn::make('aging_bucket')
                    ->label('الفئة')
                    ->badge()
                    ->color(fn (Booking $record): string => match (true) {
                        $this->getAgingDays($record) <= 30 => 'success',
                        $this->getAgingDays($record) <= 60 => 'warning',
                        $this->getAgingDays($record) <= 90 => 'danger',
                        default => 'gray',
                    })
                    ->getStateUsing(fn (Booking $record): string => match (true) {
                        $this->getAgingDays($record) <= 30 => '0-30 يوم',
                        $this->getAgingDays($record) <= 60 => '30-60 يوم',
                        $this->getAgingDays($record) <= 90 => '60-90 يوم',
                        default => 'أكثر من 90 يوم',
                    }),
            ])
            ->defaultSort('due_date', 'asc')
            ->filters([
                Filter::make('report_date')
                    ->label('تاريخ التقرير')
                    ->schema([
                        DatePicker::make('as_of_date')
                            ->label('تاريخ التقرير')
                            ->default(now())
                            ->displayFormat('Y-m-d')
                            ->native(false),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query),
                SelectFilter::make('client_id')
                    ->label('العميل')
                    ->relationship('client', 'name')
                    ->searchable(),
            ]);
    }

    protected function getReportDate(): Carbon
    {
        $asOfDate = $this->tableFilters['report_date']['as_of_date'] ?? null;

        return $asOfDate ? Carbon::parse($asOfDate) : Carbon::now();
    }

    protected function getAgingDays(Booking $booking): int
    {
        $reportDate = $this->getReportDate();
        $dueDate = $booking->due_date;

        if (! $dueDate) {
            return 0;
        }

        if ($dueDate->greaterThanOrEqualTo($reportDate->startOfDay())) {
            return 0;
        }

        return (int) $reportDate->startOfDay()->diffInDays($dueDate->startOfDay());
    }

    public function getAgingSummary(): array
    {
        $asOfDate = $this->tableFilters['report_date']['as_of_date'] ?? now()->format('Y-m-d');
        $clientId = $this->tableFilters['client_id']['value'] ?? null;

        return ClientAgingSummaryWidget::computeSummary($asOfDate, $clientId);
    }
}
