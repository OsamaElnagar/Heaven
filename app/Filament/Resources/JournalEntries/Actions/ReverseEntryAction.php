<?php

namespace App\Filament\Resources\JournalEntries\Actions;

use App\Enums\JournalEntryStatus;
use App\Models\JournalEntry;
use App\Services\JournalService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ReverseEntryAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'reverseEntry';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('عكس القيد')
            ->icon('heroicon-o-arrow-uturn-left')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('عكس القيد')
            ->modalDescription('هل أنت متأكد من عكس هذا القيد؟ سيتم إنشاء قيد عكسي جديد.')
            ->modalSubmitActionLabel('نعم، عكس القيد')
            ->visible(fn (JournalEntry $record): bool => $record->status === JournalEntryStatus::POSTED)
            ->action(function (JournalEntry $record): void {
                app(JournalService::class)->reverse($record->id);

                Notification::make()
                    ->title('تم عكس القيد بنجاح')
                    ->success()
                    ->send();
            });
    }
}
