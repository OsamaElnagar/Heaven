<?php

namespace App\Filament\Pages;

use App\Enums\PackageType;
use App\Models\Package;
use App\Models\Trip;
use App\Services\ReportService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;

class ReportsPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static \UnitEnum|string|null $navigationGroup = 'التقارير';

    protected static ?string $navigationLabel = 'التقارير';

    protected static ?string $title = 'التقارير والإحصائيات';

    protected string $view = 'filament.pages.reports';

    public int $year;

    public ?string $type = null;

    public array $report = [];

    public array $visaReport = [];

    public function mount(): void
    {
        $this->year = (int) (request('year', now()->year));
        $this->type = request('type');
        $this->report = (new ReportService)->revenueReport(
            $this->year,
            $this->type ? PackageType::from($this->type) : null
        );
        $this->visaReport = Cache::remember('visa-report', now()->addMinutes(5), function () {
            $result = [];
            $trips = Trip::withCount('bookings')->latest()->limit(5)->get();
            foreach ($trips as $trip) {
                $result[] = [
                    'trip' => $trip->name,
                    'counts' => (new ReportService)->visaDashboard($trip),
                ];
            }

            return $result;
        });
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('filter')
                ->label('تصفية')
                ->icon('heroicon-o-funnel')
                ->form([
                    TextInput::make('year')
                        ->label('السنة')
                        ->numeric()
                        ->default($this->year)
                        ->required(),
                    Select::make('type')
                        ->label('نوع الباقة')
                        ->options([
                            '' => 'الكل',
                            PackageType::HAJJ->value => PackageType::HAJJ->getLabel(),
                            PackageType::UMRAH->value => PackageType::UMRAH->getLabel(),
                        ])
                        ->default($this->type ?? '')
                        ->native(false),
                ])
                ->action(function (array $data) {
                    $this->year = (int) $data['year'];
                    $this->type = $data['type'] ?: null;
                    $this->report = (new ReportService)->revenueReport(
                        $this->year,
                        $this->type ? PackageType::from($this->type) : null
                    );
                }),

            Action::make('exportPdf')
                ->label('تصدير PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->action(function () {
                    $pdf = PDF::loadView('pdf.revenue-report', [
                        'report' => $this->report,
                        'year' => $this->year,
                        'generatedAt' => now()->format('Y-m-d h:i A'),
                    ]);

                    return response()->streamDownload(
                        fn () => print ($pdf->output()),
                        'revenue-report-'.$this->year.'.pdf'
                    );
                }),
        ];
    }

    public function table(Table $table): Table
    {
        $packages = collect($this->report['packages'] ?? []);

        return $table
            ->query(
                Package::whereIn('id', $packages->pluck('package_id')->filter()->all() ?: [-1])
            )
            ->columns([
                TextColumn::make('name')
                    ->label('الباقة')
                    ->searchable(),
                TextColumn::make('type')
                    ->label('النوع')
                    ->badge(),
                TextColumn::make('bookings')
                    ->label('عدد الحجوزات')
                    ->state(function ($record) use ($packages) {
                        $pkg = $packages->firstWhere('package_id', $record->id);

                        return $pkg['bookings'] ?? 0;
                    })
                    ->numeric(),
                TextColumn::make('collected')
                    ->label('المحصل')
                    ->state(function ($record) use ($packages) {
                        $pkg = $packages->firstWhere('package_id', $record->id);

                        return number_format($pkg['collected'] ?? 0).' ج.م';
                    }),
                TextColumn::make('outstanding')
                    ->label('المستحق')
                    ->state(function ($record) use ($packages) {
                        $pkg = $packages->firstWhere('package_id', $record->id);

                        return number_format($pkg['outstanding'] ?? 0).' ج.م';
                    }),
            ]);
    }
}
