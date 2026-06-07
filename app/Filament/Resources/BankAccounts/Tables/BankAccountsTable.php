<?php

namespace App\Filament\Resources\BankAccounts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class BankAccountsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('الرقم')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('bank_name')
                    ->label('اسم البنك')
                    ->searchable(),
                TextColumn::make('branch')
                    ->label('الفرع')
                    ->searchable(),
                TextColumn::make('account_number')
                    ->label('رقم الحساب')
                    ->searchable(),
                TextColumn::make('iban')
                    ->label('الآيبان')
                    ->searchable(),
                TextColumn::make('account.name')
                    ->label('حساب الأستاذ')
                    ->searchable(),
                ToggleColumn::make('is_active')
                    ->label('نشط'),
                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
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
            ]);
    }
}
