<?php

namespace App\Filament\Resources\Posts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('الصورة')
                    ->disk('public')
                    ->circular(),

                TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('excerpt')
                    ->label('المقتطف')
                    ->limit(60)
                    ->toggleable(),

                TextColumn::make('published_at')
                    ->label('تاريخ النشر')
                    ->date('Y/m/d')
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
