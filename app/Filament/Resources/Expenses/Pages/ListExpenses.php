<?php

namespace App\Filament\Resources\Expenses\Pages;

use App\Filament\Resources\Expenses\Actions\ExportExpensesAction;
use App\Filament\Resources\Expenses\ExpenseResource;
use App\Filament\Resources\Expenses\Widgets\ExpensesStatsWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExpenses extends ListRecords
{
    protected static string $resource = ExpenseResource::class;

    protected static ?string $title = 'سجل المصروفات';

    protected function getHeaderActions(): array
    {
        return [
            ExportExpensesAction::make(),
            CreateAction::make()->label('مصروف جديد'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ExpensesStatsWidget::class,
        ];
    }
}
