<?php

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BookingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('معلومات الحجز')
                    ->components([
                        TextEntry::make('reference')
                            ->label('رقم المرجع'),
                        TextEntry::make('client.name')
                            ->label('العميل'),
                        TextEntry::make('package.name')
                            ->label('الباقة'),
                        TextEntry::make('trip.name')
                            ->label('الرحلة')
                            ->placeholder('-'),
                        TextEntry::make('status')
                            ->label('الحالة')
                            ->badge(),
                        TextEntry::make('room_type')
                            ->label('نوع الغرفة')
                            ->badge(),
                    ])
                    ->columns(2),

                Section::make('التسعير')
                    ->components([
                        TextEntry::make('total_price')
                            ->label('السعر الإجمالي')
                            ->money('EGP'),
                        TextEntry::make('discount')
                            ->label('الخصم')
                            ->money('EGP'),
                        TextEntry::make('net_price')
                            ->label('صافي السعر')
                            ->money('EGP'),
                        TextEntry::make('paid_amount')
                            ->label('المبلغ المدفوع')
                            ->money('EGP'),
                        TextEntry::make('remaining')
                            ->label('المتبقي')
                            ->money('EGP'),
                    ])
                    ->columns(2),

                Section::make('معلومات إضافية')
                    ->components([
                        TextEntry::make('room.room_number')
                            ->label('الغرفة')
                            ->placeholder('-'),
                        TextEntry::make('due_date')
                            ->label('تاريخ الاستحقاق')
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('notes')
                            ->label('ملاحظات')
                            ->markdown()
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
