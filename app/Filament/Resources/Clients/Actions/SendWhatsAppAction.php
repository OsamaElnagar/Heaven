<?php

namespace App\Filament\Resources\Clients\Actions;

use Filament\Actions\Action;

class SendWhatsAppAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'sendWhatsApp';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('واتساب')
            ->icon('heroicon-o-chat-bubble-left-ellipsis')
            ->color('success')
            ->url(fn ($record) => 'https://wa.me/'.$record->phone)
            ->openUrlInNewTab();
    }
}
