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
                Section::make('معلومات الموظف')
                    ->components([
                        TextEntry::make('name')
                            ->label('الاسم'),
                        TextEntry::make('code')
                            ->label('كود الموظف'),
                        TextEntry::make('job_title')
                            ->label('المسمى الوظيفي')
                            ->placeholder('—'),
                        TextEntry::make('national_id')
                            ->label('الرقم القومي'),
                        TextEntry::make('phone')
                            ->label('رقم الهاتف'),
                        TextEntry::make('email')
                            ->label('البريد الإلكتروني')
                            ->placeholder('—'),
                        TextEntry::make('department.name')
                            ->label('القسم')
                            ->placeholder('—'),
                        TextEntry::make('type')
                            ->label('النوع')
                            ->badge(),
                        TextEntry::make('salary_type')
                            ->label('نوع المرتب')
                            ->badge(),
                        TextEntry::make('base_salary')
                            ->label('الراتب الأساسي')
                            ->money('EGP'),
                        TextEntry::make('daily_hours')
                            ->label('ساعات العمل')
                            ->suffix(' ساعة'),
                        TextEntry::make('hire_date')
                            ->label('تاريخ التعيين')
                            ->date(),
                        TextEntry::make('termination_date')
                            ->label('تاريخ الإنهاء')
                            ->date()
                            ->placeholder('—'),
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
                            ->placeholder('—'),
                        TextEntry::make('account.code')
                            ->label('رقم الحساب')
                            ->placeholder('—'),
                        TextEntry::make('notes')
                            ->label('ملاحظات')
                            ->html()
                            ->placeholder('—'),
                    ])
                    ->columns(2),
            ]);
    }
}
