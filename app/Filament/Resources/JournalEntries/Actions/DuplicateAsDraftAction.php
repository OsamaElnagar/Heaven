<?php

namespace App\Filament\Resources\JournalEntries\Actions;

use App\Filament\Resources\JournalEntries\JournalEntryResource;
use App\Models\JournalEntry;
use App\Services\JournalService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class DuplicateAsDraftAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'duplicateAsDraft';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('نسخ كمسودة')
            ->icon('heroicon-o-document-duplicate')
            ->color('gray')
            ->action(function (JournalEntry $record) {
                $draft = app(JournalService::class)->duplicateAsDraft($record->id);

                Notification::make()
                    ->title('تم نسخ القيد كمسودة بنجاح')
                    ->success()
                    ->send();

                return redirect(JournalEntryResource::getUrl('edit', ['record' => $draft]));
            });
    }
}
