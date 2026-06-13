<?php

namespace App\Filament\Resources\JournalEntries\Tables;

use App\Enums\JournalEntrySourceType;
use App\Enums\JournalEntryStatus;
use App\Filament\Components\Filters\DateRangeFilter;
use App\Filament\Resources\JournalEntries\Actions\PostEntryAction;
use App\Models\JournalEntry;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class JournalEntriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->label('الرقم')
                    ->copyable()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('entry_date')
                    ->label('تاريخ القيد')
                    ->date()
                    ->sortable(),
                TextColumn::make('fiscalYear.name')
                    ->label('السنة المالية')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('—'),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('source_type')
                    ->label('نوع المصدر')
                    ->badge()
                    ->searchable(),
                TextColumn::make('total_debits')
                    ->label('إجمالي المدين')
                    ->state(fn (JournalEntry $record): string => number_format($record->totalDebits()))
                    ->sortable(false),
                TextColumn::make('total_credits')
                    ->label('إجمالي الدائن')
                    ->state(fn (JournalEntry $record): string => number_format($record->totalCredits()))
                    ->sortable(false),
                TextColumn::make('description')
                    ->label('الوصف')
                    ->searchable()
                    ->limit(40)
                    ->toggleable(),
                TextColumn::make('createdBy.name')
                    ->label('أنشأه')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('—'),
                TextColumn::make('posted_at')
                    ->label('تاريخ الترحيل')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('entry_date', 'desc')
            ->filters([
                DateRangeFilter::make('entry_date')
                    ->label('تاريخ القيد'),
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(JournalEntryStatus::class),
                SelectFilter::make('source_type')
                    ->label('نوع المصدر')
                    ->options(JournalEntrySourceType::class),
                SelectFilter::make('fiscal_year_id')
                    ->label('السنة المالية')
                    ->relationship('fiscalYear', 'name')
                    ->searchable()
                    ->preload(),
                TrashedFilter::make(),
            ])
            ->recordActions([
                PostEntryAction::make(),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
