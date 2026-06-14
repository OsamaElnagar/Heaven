<?php

namespace App\Filament\Resources\EmployeeAdvances\Tables;

use App\Enums\EmployeeAdvanceStatus;
use App\Filament\Components\Filters\DateRangeFilter;
use App\Filament\Resources\Employees\EmployeeResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EmployeeAdvancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->copyable()
                    ->label('الرقم')
                    ->searchable(),
                TextColumn::make('employee.name')
                    ->label('الموظف')
                    ->url(fn ($record) => $record->employee ? EmployeeResource::getUrl('edit', ['record' => $record->employee]) : null, true)
                    ->color('info')
                    ->icon(Heroicon::ArrowUpRight)
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('advance_date')
                    ->label('تاريخ السلفة')
                    ->date(),
                TextColumn::make('amount')
                    ->label('المبلغ')
                    ->money('EGP', locale: 'en', decimalPlaces: 0)
                    ->sortable(),
                TextColumn::make('repaid_amount')
                    ->label('المبلغ المُسدّد')
                    ->money('EGP', locale: 'en', decimalPlaces: 0),
                TextColumn::make('installments')
                    ->label('عدد الأقساط')
                    ->numeric(),
                TextColumn::make('type')
                    ->label('النوع')
                    ->badge(),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge(),
                TextColumn::make('notes')
                    ->label('ملاحظات')
                    ->searchable()
                    ->limit(50),
            ])
            ->filters([
                DateRangeFilter::make('advance_date', 'advance_date')
                    ->label('تاريخ السلفة'),
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(EmployeeAdvanceStatus::class),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
