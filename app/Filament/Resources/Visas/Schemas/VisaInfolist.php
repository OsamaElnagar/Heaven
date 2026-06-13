<?php

namespace App\Filament\Resources\Visas\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VisaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات التأشيرة')
                    ->components([
                        TextEntry::make('booking.reference')
                            ->label('الحجز'),
                        TextEntry::make('booking.client.name')
                            ->label('العميل'),
                        TextEntry::make('status')
                            ->label('الحالة')
                            ->badge(),
                        TextEntry::make('visa_number')
                            ->label('رقم التأشيرة')
                            ->placeholder('—'),
                    ])
                    ->columns(2),

                Section::make('التواريخ')
                    ->components([
                        TextEntry::make('applied_at')
                            ->label('تاريخ التقديم')
                            ->date()
                            ->placeholder('—'),
                        TextEntry::make('approved_at')
                            ->label('تاريخ الموافقة')
                            ->date()
                            ->placeholder('—'),
                        TextEntry::make('expiry_date')
                            ->label('تاريخ الانتهاء')
                            ->date()
                            ->placeholder('—'),
                    ])
                    ->columns(2),

                Section::make('ملاحظات')
                    ->components([
                        TextEntry::make('rejection_reason')
                            ->label('سبب الرفض')
                            ->placeholder('—')
                            ->columnSpanFull(),
                        TextEntry::make('notes')
                            ->label('ملاحظات')
                            ->placeholder('—')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
