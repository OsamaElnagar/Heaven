<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Filament\Resources\Payments\Actions\ExportLedgerAction;
use App\Filament\Resources\Payments\PaymentResource;
use App\Filament\Resources\Payments\Widgets\PaymentsStatsWidget;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    protected static ?string $title = 'سجل المدفوعات';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('دفعة جديدة'),
            Action::make('revenueReport')
                ->label('تقرير الإيرادات')
                ->icon('heroicon-o-document-chart-bar')
                ->color('info')
                ->url(PaymentResource::getUrl('revenue-report')),
            ExportLedgerAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PaymentsStatsWidget::class,
        ];
    }
}
