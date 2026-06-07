<?php

namespace App\Filament\Resources\Accounts\Tables;

use App\Enums\AccountClass;
use App\Enums\AccountType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class AccountsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('الرقم')
                    ->searchable()
                    ->copyable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name_en')
                    ->label('الاسم بالإنجليزية')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('class')
                    ->label('الفئة')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('النوع')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('normal_balance')
                    ->label('الرصيد الطبيعي')
                    ->badge()
                    ->searchable(),
                TextColumn::make('parent.name')
                    ->label('الحساب الأب')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('level')
                    ->label('المستوى')
                    ->numeric()
                    ->sortable(),
                ToggleColumn::make('is_active')
                    ->label('نشط'),
                IconColumn::make('is_system')
                    ->label('نظامي')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('class')
                    ->label('الفئة')
                    ->options(AccountClass::class)
                    ->searchable(),
                SelectFilter::make('type')
                    ->label('النوع')
                    ->options(AccountType::class)
                    ->searchable(),
                TrashedFilter::make(),
                Filter::make('is_active')
                    ->label('نشط فقط')
                    ->query(fn ($query) => $query->where('is_active', true)),
                Filter::make('is_system')
                    ->label('نظامي')
                    ->query(fn ($query) => $query->where('is_system', true)),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('code');
    }
}
