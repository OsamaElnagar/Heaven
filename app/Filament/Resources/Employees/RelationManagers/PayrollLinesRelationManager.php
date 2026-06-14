<?php

namespace App\Filament\Resources\Employees\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PayrollLinesRelationManager extends RelationManager
{
    protected static string $relationship = 'payrollLines';

    protected static ?string $title = 'سجل الرواتب';

    protected static ?string $modelLabel = 'سجل راتب';

    protected static ?string $pluralModelLabel = 'سجلات رواتب';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('payrollRun.code')
                    ->label('رقم المسير'),
                TextColumn::make('payrollRun.month')
                    ->label('الشهر')
                    ->formatStateUsing(fn ($state, $record) => $state.'/'.$record->payrollRun->year),
                TextColumn::make('gross_salary')
                    ->numeric()
                    ->label('إجمالي المستحق'),
                TextColumn::make('advances_deduction')
                    ->numeric()
                    ->label('السلف'),
                TextColumn::make('net_salary')
                    ->numeric()
                    ->label('الصافي'),
                TextColumn::make('paid_amount')
                    ->numeric()
                    ->label('المدفوع'),
                TextColumn::make('remaining_amount')
                    ->numeric()
                    ->label('المتبقي'),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([]);
    }
}
