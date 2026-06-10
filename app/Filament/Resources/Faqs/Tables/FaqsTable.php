<?php

namespace App\Filament\Resources\Faqs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class FaqsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('question')
                    ->label('السؤال')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('answer')
                    ->label('الإجابة')
                    ->limit(60)
                    ->toggleable(),

                TextColumn::make('sort_order')
                    ->label('الترتيب')
                    ->sortable()
                    ->toggleable(),

                ToggleColumn::make('is_published')
                    ->label('منشور'),
            ])
            ->filters([
                TernaryFilter::make('is_published')
                    ->label('منشور'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
