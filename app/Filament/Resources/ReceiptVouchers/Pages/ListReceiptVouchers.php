<?php

namespace App\Filament\Resources\ReceiptVouchers\Pages;

use App\Enums\ExpenseStatus;
use App\Filament\Resources\ReceiptVouchers\ReceiptVoucherResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListReceiptVouchers extends ListRecords
{
    protected static string $resource = ReceiptVoucherResource::class;

    protected static ?string $title = 'سندات القبض';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('إنشاء سند قبض'),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('الكل')->icon('heroicon-m-rectangle-stack'),
        ];

        foreach (ExpenseStatus::cases() as $status) {
            $tabs[$status->value] = Tab::make($status->getLabel())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', $status->value))
                ->icon($status->getIcon());
        }

        return $tabs;
    }
}
