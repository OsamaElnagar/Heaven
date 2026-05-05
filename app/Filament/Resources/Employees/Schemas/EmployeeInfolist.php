<?php

namespace App\Filament\Resources\Employees\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EmployeeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('البيانات الشخصية')
                    ->components([
                        TextEntry::make('name')
                            ->label('الاسم'),
                        TextEntry::make('national_id')
                            ->label('الرقم القومي'),
                        TextEntry::make('phone')
                            ->label('رقم الهاتف'),
                        TextEntry::make('role')
                            ->label('المسمى الوظيفي'),
                    ])
                    ->columns(2),

                Section::make('الراتب والتوظيف')
                    ->components([
                        TextEntry::make('salary_type')
                            ->label('نوع الراتب')
                            ->badge(),
                        TextEntry::make('salary')
                            ->label('الراتب')
                            ->money('EGP'),
                        TextEntry::make('hired_at')
                            ->label('تاريخ التعيين')
                            ->date(),
                        TextEntry::make('left_at')
                            ->label('تاريخ ترك العمل')
                            ->date()
                            ->placeholder('-'),
                        IconEntry::make('is_active')
                            ->label('نشط')
                            ->boolean(),
                    ])
                    ->columns(2),

                Section::make('حساب المستخدم')
                    ->components([
                        TextEntry::make('user.name')
                            ->label('حساب المستخدم')
                            ->placeholder('غير مرتبط'),
                        TextEntry::make('user.email')
                            ->label('البريد الإلكتروني')
                            ->placeholder('-'),
                    ])
                    ->columns(2),
            ]);
    }
}
