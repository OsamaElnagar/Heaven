<?php

namespace App\Filament\Resources\Employees\Tables;

use App\Enums\EmployeeType;
use App\Enums\SalaryType;
use App\Filament\Components\Filters\DateRangeFilter;
use App\Filament\Resources\Employees\Actions\DeactivateEmployeeAction;
use App\Models\Employee;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class EmployeesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('الرقم')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('job_title')
                    ->label('المسمى الوظيفي')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('national_id')
                    ->label('الرقم القومي')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone')
                    ->label('الهاتف')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('department.name')
                    ->label('القسم')
                    ->searchable(),
                TextColumn::make('type')
                    ->label('النوع')
                    ->badge(),
                TextColumn::make('salary_type')
                    ->label('نوع المرتب')
                    ->badge(),
                TextColumn::make('base_salary')
                    ->label('الراتب الأساسي')
                    ->money('EGP')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ToggleColumn::make('is_active')
                    ->label('نشط'),
                TextColumn::make('hire_date')
                    ->label('تاريخ التعيين')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('نوع الموظف')
                    ->options(EmployeeType::class),
                SelectFilter::make('department_id')
                    ->label('القسم')
                    ->relationship('department', 'name'),
                SelectFilter::make('role')
                    ->label('الدور')
                    ->multiple()
                    ->options(
                        fn(): array => Employee::query()
                            ->distinct()
                            ->whereNotNull('role')
                            ->orderBy('role')
                            ->pluck('role', 'role')
                            ->toArray()
                    ),
                SelectFilter::make('salary_type')
                    ->label('نوع الراتب')
                    ->options(SalaryType::class),
                SelectFilter::make('is_active')
                    ->label('الحالة')
                    ->options([
                        '1' => 'نشط',
                        '0' => 'غير نشط',
                    ]),

                // DateRangeFilter::make('hire_date')
                //     ->label('تاريخ التعيين'),
                // DateRangeFilter::make('termination_date')
                //     ->label('تاريخ انتهاء الخدمة'),

                TrashedFilter::make(),
            ])
            ->recordActions([
                DeactivateEmployeeAction::make(),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('hire_date', 'desc');
    }
}
