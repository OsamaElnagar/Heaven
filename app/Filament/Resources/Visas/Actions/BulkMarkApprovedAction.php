<?php

namespace App\Filament\Resources\Visas\Actions;

use App\Enums\VisaStatus;
use Carbon\Carbon;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\DatePicker;

class BulkMarkApprovedAction extends BulkActionGroup
{
    public static function make(array $actions = []): static
    {
        return parent::make([
            BulkAction::make('bulkMarkApproved')
                ->label('موافقة جماعية')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->schema([
                    DatePicker::make('expiry_date')
                        ->label('تاريخ الانتهاء')
                        ->required()
                        ->native(false),
                ])
                ->action(function ($records, array $data) {
                    $records->each(function ($visa) use ($data) {
                        if ($visa->status !== VisaStatus::APPROVED) {
                            $visa->update([
                                'status' => VisaStatus::APPROVED,
                                'expiry_date' => Carbon::parse($data['expiry_date']),
                            ]);
                        }
                    });
                }),
        ]);
    }
}
