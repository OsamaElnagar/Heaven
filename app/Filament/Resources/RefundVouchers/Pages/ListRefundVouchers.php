<?php

namespace App\Filament\Resources\RefundVouchers\Pages;

use App\Enums\ExpenseStatus;
use App\Filament\Resources\RefundVouchers\RefundVoucherResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListRefundVouchers extends ListRecords
{
    protected static string $resource = RefundVoucherResource::class;

    protected static ?string $title = 'سندات الاسترداد';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('إنشاء سند استرداد'),
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
