<?php

namespace App\Filament\Resources\JournalEntries\Pages;

use App\Enums\JournalEntryStatus;
use App\Filament\Resources\JournalEntries\Actions\DuplicateAsDraftAction;
use App\Filament\Resources\JournalEntries\Actions\PostEntryAction;
use App\Filament\Resources\JournalEntries\Actions\ReverseEntryAction;
use App\Filament\Resources\JournalEntries\JournalEntryResource;
use App\Models\JournalEntry;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditJournalEntry extends EditRecord
{
    protected static string $resource = JournalEntryResource::class;

    protected static ?string $title = 'تعديل قيد يومية';

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $entry = $this->getRecord();

        if ($entry->status !== JournalEntryStatus::DRAFT) {
            Notification::make()
                ->warning()
                ->title('هذا القيد '.$entry->status->getLabel().' — يمكنك عكسه أو نسخه من الأزرار أعلاه.')
                ->send();
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            PostEntryAction::make()
                ->visible(fn (JournalEntry $record): bool => $record->status === JournalEntryStatus::DRAFT),
            ReverseEntryAction::make(),
            DuplicateAsDraftAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
