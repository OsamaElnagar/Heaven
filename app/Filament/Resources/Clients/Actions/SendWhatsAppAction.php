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
            ->url(function ($record): ?string {
                $phone = $record->phone;

                if (blank($phone)) {
                    return null;
                }

                $phone = preg_replace('/[\s\-]/', '', $phone);

                if (str_starts_with($phone, '+20')) {
                    return 'https://wa.me/'.$phone;
                }

                if (str_starts_with($phone, '0')) {
                    return 'https://wa.me/20'.$phone;
                }

                return null;
            })
            ->openUrlInNewTab();
    }
}
