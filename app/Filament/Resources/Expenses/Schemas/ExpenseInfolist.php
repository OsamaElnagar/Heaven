<?php

namespace App\Filament\Resources\Expenses\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExpenseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات المصروف')
                    ->components([
                        TextEntry::make('trip.name')
                            ->label('الرحلة')
                            ->placeholder('—'),
                        TextEntry::make('category')
                            ->label('الفئة')
                            ->badge(),
                        TextEntry::make('description')
                            ->label('الوصف'),
                        TextEntry::make('amount')
                            ->label('المبلغ')
                            ->money('EGP'),
                    ])
                    ->columns(2),

                Section::make('تفاصيل الدفع')
                    ->components([
                        TextEntry::make('payment_method')
                            ->label('طريقة الدفع')
                            ->badge(),
                        TextEntry::make('paid_at')
                            ->label('تاريخ الدفع')
                            ->date(),
                        TextEntry::make('paidBy.name')
                            ->label('مدفوع بواسطة')
                            ->placeholder('—'),
                    ])
                    ->columns(2),
            ]);
    }
}
