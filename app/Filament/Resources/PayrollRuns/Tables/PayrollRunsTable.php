<?php

namespace App\Filament\Resources\PayrollRuns\Tables;

use App\Enums\PayrollRunStatus;
use App\Enums\PayrollRunType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PayrollRunsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->copyable()
                    ->label('الرقم')
                    ->searchable(),
                TextColumn::make('fiscalYear.name')
                    ->label('السنة المالية'),
                TextColumn::make('month')
                    ->label('الشهر')
                    ->sortable(),
                TextColumn::make('year')
                    ->label('السنة')
                    ->sortable(),
                TextColumn::make('type')
                    ->label('النوع')
                    ->badge(),
                TextColumn::make('total_gross')
                    ->label('إجمالي المستحقات')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_deductions')
                    ->label('إجمالي الخصومات')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_net')
                    ->label('صافي المستحقات')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(PayrollRunStatus::class),
                SelectFilter::make('type')
                    ->label('النوع')
                    ->options(PayrollRunType::class),
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
