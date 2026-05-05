<?php

namespace App\Filament\Resources\Trips\Pages;

use App\Filament\Resources\Trips\Actions\BulkSubmitVisasAction;
use App\Filament\Resources\Trips\Actions\CompleteTripAction;
use App\Filament\Resources\Trips\Actions\DepartTripAction;
use App\Filament\Resources\Trips\Actions\ExportManifestAction;
use App\Filament\Resources\Trips\Actions\ExportRoomingListAction;
use App\Filament\Resources\Trips\TripResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTrip extends ViewRecord
{
    protected static string $resource = TripResource::class;

    protected static ?string $title = 'عرض رحلة';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('dashboard')
                ->label('لوحة المعلومات')
                ->icon('heroicon-o-presentation-chart-bar')
                ->color('info')
                ->url(fn () => TripResource::getUrl('dashboard', ['record' => $this->record])),
            Action::make('manifest')
                ->label('كشف المسافرين')
                ->icon('heroicon-o-clipboard-document-list')
                ->color('success')
                ->url(fn () => TripResource::getUrl('manifest', ['record' => $this->record])),
            Action::make('rooming')
                ->label('توزيع الغرف')
                ->icon('heroicon-o-building-office-2')
                ->color('warning')
                ->url(fn () => TripResource::getUrl('rooming', ['record' => $this->record])),
            EditAction::make()->label('تعديل'),
            DepartTripAction::make(),
            CompleteTripAction::make(),
            ExportManifestAction::make(),
            ExportRoomingListAction::make(),
            BulkSubmitVisasAction::make(),
        ];
    }
}
