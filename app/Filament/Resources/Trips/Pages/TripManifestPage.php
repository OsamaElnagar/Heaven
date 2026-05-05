<?php

namespace App\Filament\Resources\Trips\Pages;

use App\Filament\Resources\Trips\TripResource;
use App\Services\TripService;
use Filament\Actions\Action;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;

class TripManifestPage extends Page implements HasTable
{
    use InteractsWithRecord;
    use InteractsWithTable;

    protected static string $resource = TripResource::class;

    protected static ?string $title = 'كشف المسافرين';

    protected string $view = 'filament.resources.trips.pages.trip-manifest';

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
                ->action(function () {
                    $bookings = (new TripService)->getManifest($this->record);
                    $pdf = PDF::loadView('pdf.trip-manifest', [
                        'trip' => $this->record,
                        'bookings' => $bookings,
                        'generatedAt' => now()->format('Y-m-d h:i A'),
                    ]);

                    return response()->streamDownload(
                        fn () => print ($pdf->output()),
                        'manifest-trip-'.$this->record->id.'.pdf'
                    );
                }),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => $this->record->bookings()->where('status', 'confirmed')->with(['client', 'visa']))
            ->columns([
                TextColumn::make('client.name')->label('الاسم')->searchable(),
                TextColumn::make('client.passport_number')->label('رقم الجواز')->searchable(),
                TextColumn::make('client.national_id')->label('الرقم القومي')->searchable(),
                TextColumn::make('visa.visa_number')->label('رقم التأشيرة')->placeholder('-'),
                TextColumn::make('visa.status')->label('حالة التأشيرة')->badge(),
                TextColumn::make('room_type')->label('نوع الغرفة')->badge(),
            ]);
    }
}
