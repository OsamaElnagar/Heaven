<?php

namespace App\Filament\Resources\FiscalYears\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class JournalEntriesRelationManager extends RelationManager
{
    protected static string $relationship = 'journalEntries';

    protected static ?string $title = 'قيود اليومية';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->label('الرقم')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('entry_date')
                    ->label('تاريخ القيد')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->sortable(),
                TextColumn::make('source_type')
                    ->label('نوع المصدر')
                    ->badge(),
                TextColumn::make('description')
                    ->label('الوصف')
                    ->searchable()
                    ->limit(40),
            ])
            ->defaultSort('entry_date', 'desc')
            ->headerActions([])
            ->recordActions([]);
    }
}
