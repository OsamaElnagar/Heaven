<?php

namespace App\Filament\Resources\Clients\Tables;

use App\Enums\Gender;
use App\Enums\MaritalStatus;
use App\Filament\Resources\Clients\Actions\SendWhatsAppAction;
use App\Models\Client;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('national_id')
                    ->label('الرقم القومي')
                    ->searchable()
                    ->copyable()
                    ->sortable(),
                TextColumn::make('passport_number')
                    ->label('جواز السفر')
                    ->searchable()
                    ->copyable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label('الهاتف')
                    ->searchable()
                    ->copyable()
                    ->sortable(),
                TextColumn::make('governorate')
                    ->label('المحافظة')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('gender')
                    ->label('الجنس')
                    ->badge()
                    ->searchable(),
                TextColumn::make('passport_expiry')
                    ->label('انتهاء الجواز')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('gender')
                    ->label('الجنس')
                    ->options(Gender::class),
                SelectFilter::make('marital_status')
                    ->label('الحالة الاجتماعية')
                    ->options(MaritalStatus::class),
                SelectFilter::make('governorate')
                    ->label('المحافظة')
                    ->options(fn () => Client::query()
                        ->distinct()
                        ->whereNotNull('governorate')
                        ->pluck('governorate', 'governorate')
                    ),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                SendWhatsAppAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
