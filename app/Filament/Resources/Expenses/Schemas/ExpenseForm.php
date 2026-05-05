<?php

namespace App\Filament\Resources\Expenses\Schemas;

use App\Enums\PaymentMethod;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات المصروف')
                    ->components([
                        Select::make('trip_id')
                            ->label('الرحلة')
                            ->relationship('trip', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false),
                        Select::make('category')
                            ->label('الفئة')
                            ->options([
                                'office' => 'مكتب',
                                'marketing' => 'تسويق',
                                'transport' => 'نقل',
                                'hotel_cost' => 'تكلفة فندق',
                                'airline_cost' => 'تكلفة طيران',
                                'other' => 'أخرى',
                            ])
                            ->required()
                            ->native(false),
                        TextInput::make('description')
                            ->label('الوصف')
                            ->required(),
                        TextInput::make('amount')
                            ->label('المبلغ')
                            ->required()
                            ->numeric()
                            ->prefix('ج.م'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('تفاصيل الدفع')
                    ->components([
                        Select::make('payment_method')
                            ->label('طريقة الدفع')
                            ->options(PaymentMethod::class)
                            ->required()
                            ->native(false),
                        DatePicker::make('paid_at')
                            ->label('تاريخ الدفع')
                            ->required()
                            ->default(now())
                            ->native(false),
                        Select::make('paid_by')
                            ->label('مدفوع بواسطة')
                            ->relationship('paidBy', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('ملاحظات')
                    ->components([
                        Textarea::make('notes')
                            ->label('ملاحظات')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
