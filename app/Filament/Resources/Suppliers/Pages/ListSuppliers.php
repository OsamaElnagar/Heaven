<?php

namespace App\Filament\Resources\Suppliers\Pages;

use App\Enums\SupplierType;
use App\Filament\Resources\Suppliers\SupplierResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListSuppliers extends ListRecords
{
    protected static string $resource = SupplierResource::class;

    protected static ?string $title = 'قائمة الموردين';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('مورد جديد'),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('الكل')->icon('heroicon-m-rectangle-stack'),
        ];

        foreach (SupplierType::cases() as $status) {
            $tabs[$status->value] = Tab::make($status->getLabel())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', $status->value))
                ->icon($status->getIcon());
        }

        return $tabs;
    }
}
