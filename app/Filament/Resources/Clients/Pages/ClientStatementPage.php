<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use App\Services\ReportService;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;

class ClientStatementPage extends Page implements HasTable
{
    use InteractsWithRecord;
    use InteractsWithTable;

    protected static string $resource = ClientResource::class;

    protected static ?string $title = 'كشف حساب';

    protected string $view = 'filament.resources.clients.pages.client-statement';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    public function getBreadcrumbs(): array
    {
        $resource = static::getResource();

        return [
            $resource::getUrl('index') => $resource::getBreadcrumb(),
            $resource::getUrl('view', ['record' => $this->record]) => $this->record->name,
            '#' => static::$title,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->label('طباعة')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->action(function () {
                    $report = (new ReportService)->clientStatement($this->record);
                    $pdf = PDF::loadView('pdf.client-statement', [
                        'report' => $report,
                        'generatedAt' => now()->format('Y-m-d h:i A'),
                    ]);

                    return response()->streamDownload(
                        fn () => print ($pdf->output()),
                        'statement-'.$this->record->id.'.pdf'
                    );
                }),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        $report = (new ReportService)->clientStatement($this->record);
        $totalPaid = collect($report['bookings'])->sum('paid');
        $totalRemaining = collect($report['bookings'])->sum('remaining');
        $bookingCount = count($report['bookings']);

        return $schema
            ->record($this->record)
            ->components([
                Section::make('ملخص الحساب')
                    ->schema([
                        TextEntry::make('client_name')
                            ->label('العميل')
                            ->state($this->record->name),
                        TextEntry::make('client_phone')
                            ->label('الهاتف')
                            ->state($this->record->phone),
                        TextEntry::make('booking_count')
                            ->label('عدد الحجوزات')
                            ->state($bookingCount),
                        TextEntry::make('total_paid')
                            ->label('إجمالي المدفوع')
                            ->state(number_format($totalPaid, 2).' ج.م'),
                        TextEntry::make('total_remaining')
                            ->label('المتبقي')
                            ->state(number_format($totalRemaining, 2).' ج.م'),
                    ])
                    ->columns(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => $this->record->bookings()->with(['package', 'visa']))
            ->columns([
                TextColumn::make('reference')
                    ->label('المرجع')
                    ->searchable(),
                TextColumn::make('package.name')
                    ->label('الباقة'),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge(),
                TextColumn::make('net_price')
                    ->label('الإجمالي')
                    ->money('EGP'),
                TextColumn::make('paid_amount')
                    ->label('المدفوع')
                    ->money('EGP'),
                TextColumn::make('remaining')
                    ->label('المتبقي')
                    ->money('EGP'),
                TextColumn::make('visa.status')
                    ->label('التأشيرة')
                    ->badge()
                    ->placeholder('-'),
            ])
            ->paginate(15);
    }
}
