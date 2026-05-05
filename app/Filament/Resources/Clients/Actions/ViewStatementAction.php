<?php

namespace App\Filament\Resources\Clients\Actions;

use App\Filament\Resources\Clients\ClientResource;
use Filament\Actions\Action;

class ViewStatementAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'viewStatement';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('كشف حساب')
            ->icon('heroicon-o-document-text')
            ->color('gray')
            ->url(fn ($record) => ClientResource::getUrl('statement', ['record' => $record]));
    }
}
