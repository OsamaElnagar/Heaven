<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Enums\Gender;
use App\Filament\Resources\Clients\ClientResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListClients extends ListRecords
{
    protected static string $resource = ClientResource::class;

    protected static ?string $title = 'قائمة العملاء';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('عميل جديد'),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('الكل')->icon('heroicon-m-rectangle-stack'),
        ];

        foreach (Gender::cases() as $status) {
            $tabs[$status->value] = Tab::make($status->getLabel())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('gender', $status->value))
                ->icon($status->getIcon());
        }

        return $tabs;
    }
}
