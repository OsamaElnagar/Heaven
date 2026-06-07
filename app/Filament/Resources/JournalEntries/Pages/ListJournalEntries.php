<?php

namespace App\Filament\Resources\JournalEntries\Pages;

use App\Enums\JournalEntryStatus;
use App\Filament\Resources\JournalEntries\JournalEntryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListJournalEntries extends ListRecords
{
    protected static string $resource = JournalEntryResource::class;

    protected static ?string $title = 'قيود اليومية';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('إنشاء قيد'),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('الكل')->icon('heroicon-m-rectangle-stack'),
        ];

        foreach (JournalEntryStatus::cases() as $status) {
            $tabs[$status->value] = Tab::make($status->getLabel())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', $status->value))
                ->icon($status->getIcon());
        }

        return $tabs;
    }
}
