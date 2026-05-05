<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات الدفعة')
                    ->components([
                        TextEntry::make('booking.reference')
                            ->label('الحجز'),
                        TextEntry::make('type')
                            ->label('النوع')
                            ->badge(),
                        TextEntry::make('method')
                            ->label('طريقة الدفع')
                            ->badge(),
                        TextEntry::make('amount')
                            ->label('المبلغ')
                            ->money('EGP'),
                        TextEntry::make('paid_at')
                            ->label('تاريخ الدفع')
                            ->date(),
                    ])
                    ->columns(2),

                Section::make('تفاصيل إضافية')
                    ->components([
                        TextEntry::make('reference')
                            ->label('رقم مرجعي')
                            ->placeholder('-'),
                        TextEntry::make('bank_name')
                            ->label('اسم البنك')
                            ->placeholder('-'),
                        TextEntry::make('receivedBy.name')
                            ->label('استلم بواسطة')
                            ->placeholder('-'),
                        TextEntry::make('notes')
                            ->label('ملاحظات')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
