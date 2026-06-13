<?php

namespace App\Filament\Resources\Packages\Pages;

use App\Enums\PackageGrade;
use App\Filament\Resources\Packages\PackageResource;
use App\Filament\Resources\Packages\Widgets\PackagesStatsWidget;
use App\Filament\Resources\Packages\Widgets\SeatOccupancyWidget;
use App\Models\PackageType;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPackages extends ListRecords
{
    protected static string $resource = PackageResource::class;

    protected static ?string $title = 'قائمة الباقات';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('باقة جديدة'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PackagesStatsWidget::class,
            SeatOccupancyWidget::class,
        ];
    }

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('الكل')->icon('heroicon-m-rectangle-stack'),
        ];

        foreach (PackageType::all() as $type) {
            $tabs[$type->slug] = Tab::make($type->name_ar)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type_id', $type->id))
                ->icon($type->icon);
        }
        foreach (PackageGrade::cases() as $status) {
            $tabs[$status->value] = Tab::make($status->getLabel())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('grade', $status->value))
                ->icon($status->getIcon());
        }

        return $tabs;
    }
}
