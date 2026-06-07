<?php

namespace App\Filament\Resources\JournalEntries\Actions;

use App\Enums\JournalEntryStatus;
use App\Models\JournalEntry;
use App\Services\Accounting\JournalEntryService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class PostEntryAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'postEntry';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('ترحيل')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->visible(fn (JournalEntry $record): bool => $record->status === JournalEntryStatus::DRAFT)
            ->requiresConfirmation()
            ->modalHeading('ترحيل القيد')
            ->modalDescription('هل أنت متأكد من ترحيل هذا القيد؟')
            ->modalSubmitActionLabel('نعم، ترحيل')
            ->action(function (JournalEntry $record): void {
                try {
                    app(JournalEntryService::class)->post($record, auth('web')->id());

                    Notification::make()
                        ->title('تم ترحيل القيد بنجاح')
                        ->success()
                        ->send();
                } catch (\InvalidArgumentException $e) {
                    Notification::make()
                        ->title($e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }
}
