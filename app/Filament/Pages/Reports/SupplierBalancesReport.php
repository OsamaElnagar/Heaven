<?php

namespace App\Filament\Pages\Reports;

use App\Enums\SupplierType;
use App\Filament\Pages\Reports\Widgets\SupplierBalancesSummaryWidget;
use App\Filament\Resources\Suppliers\SupplierResource;
use App\Models\Supplier;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SupplierBalancesReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-m-scale';

    protected static ?string $navigationLabel = 'أرصدة الموردين';

    protected static \UnitEnum|string|null $navigationGroup = 'التقارير';

    protected static ?int $navigationSort = 8;

    protected string $view = 'filament.pages.reports.aging-report';

    protected static ?string $title = 'تقرير أرصدة الموردين';

    public function getBreadcrumbs(): array
    {
        return ['#' => static::$title];
    }

    public function getHeaderWidgets(): array
    {
        return [SupplierBalancesSummaryWidget::class];
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
        $summary = $this->getBalancesSummary();

        $pdf = app(PdfService::class)->generateReportPdf(
            title: static::$title,
            columns: ['الكود', 'المورد', 'النوع', 'المدينة', 'الرصيد'],
            rows: $rows->map(fn (Supplier $supplier) => [
                $supplier->code,
                $supplier->name,
                $supplier->type?->getLabel() ?? '-',
                $supplier->city ?? '-',
                number_format((int) $supplier->balance),
            ])->toArray(),
            summaries: [
                ['', 'إجمالي المستحق للموردين', '', '', number_format($summary['total_owed'])],
            ],
        );

        return response()->streamDownload(fn () => print ($pdf->output()), 'تقرير-أرصدة-الموردين-'.now()->format('Y-m-d').'.pdf');
    }

    public function table(Table $table): Table
    {
        $balances = DB::table('journal_lines as jl')
            ->join('journal_entries as je', 'jl.journal_entry_id', '=', 'je.id')
            ->where('je.status', 'posted')
            ->whereNull('je.deleted_at')
            ->groupBy('jl.account_id')
            ->selectRaw('jl.account_id, SUM(jl.credit_amount) - SUM(jl.debit_amount) as balance');

        return $table
            ->query(
                Supplier::query()
                    ->whereNotNull('suppliers.account_id')
                    ->leftJoinSub($balances, 'bal', fn ($join) => $join->on('suppliers.account_id', '=', 'bal.account_id'))
                    ->addSelect('suppliers.*')
                    ->addSelect('bal.balance as balance')
                    ->with('account')
            )
            ->columns([
                TextColumn::make('code')
                    ->label('الكود')
                    ->copyable()
                    ->searchable(),
                TextColumn::make('name')
                    ->label('المورد')
                    ->url(fn (Supplier $record) => SupplierResource::getUrl('edit', ['record' => $record]))
                    ->searchable(),
                TextColumn::make('type')
                    ->label('النوع')
                    ->badge(),
                TextColumn::make('city')
                    ->label('المدينة')
                    ->placeholder('-'),
                TextColumn::make('balance')
                    ->label('الرصيد (ج.م)')
                    ->money('EGP', locale: 'en', decimalPlaces: 0)
                    ->color(fn ($state): string => (int) $state > 0 ? 'warning' : ((int) $state < 0 ? 'success' : 'gray')),
            ])
            ->defaultSort('name', 'asc')
            ->filters([
                SelectFilter::make('type')
                    ->label('النوع')
                    ->options(SupplierType::class)
                    ->query(fn (Builder $query, array $data) => $data['value'] ? $query->where('type', $data['value']) : $query),
            ]);
    }

    public function getBalancesSummary(): array
    {
        return SupplierBalancesSummaryWidget::computeSummary();
    }
}
