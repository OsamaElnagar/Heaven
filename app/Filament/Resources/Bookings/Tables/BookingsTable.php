<?php

namespace App\Filament\Resources\Bookings\Tables;

use App\Enums\BookingChannel;
use App\Enums\BookingStatus;
use App\Filament\Components\Filters\DateRangeFilter;
use App\Filament\Resources\Bookings\Actions\PrintReceiptAction;
use App\Filament\Resources\Bookings\Actions\RecordPaymentAction;
use App\Filament\Resources\Clients\ClientResource;
use App\Filament\Resources\Clients\RelationManagers\BookingsRelationManager;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class BookingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference')
                    ->label('المرجع')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                TextColumn::make('client.name')
                    ->label('العميل')
                    ->placeholder('—')
                    ->url(fn ($record) => $record->client
                        ? ClientResource::getUrl('edit', ['record' => $record->client])
                        : null, true)
                    ->icon(Heroicon::ArrowUpRight)
                    ->color('primary')
                    ->searchable()
                    ->sortable()
                    ->hiddenOn(BookingsRelationManager::class)
                    ->placeholder('—'),
                TextColumn::make('package.name')
                    ->label('الباقة')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('channel')
                    ->label('المصدر')
                    ->badge()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('branch.name')
                    ->label('الفرع')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('agent.name')
                    ->label('الوكيل')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->sortable(),
                TextColumn::make('net_price')
                    ->label('صافي السعر')
                    ->money('EGP')
                    ->sortable()
                    ->summarize(Sum::make()->label('الإجمالي')->money('EGP')),
                TextColumn::make('paid_amount')
                    ->label('المدفوع')
                    ->money('EGP')
                    ->sortable()
                    ->summarize(Sum::make()->label('الإجمالي')->money('EGP')),
                TextColumn::make('remaining')
                    ->label('المتبقي')
                    ->money('EGP')
                    ->sortable()
                    ->summarize(Sum::make()->label('الإجمالي')->money('EGP')),
                TextColumn::make('due_date')
                    ->label('الاستحقاق')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('channel')
                    ->label('المصدر')
                    ->options(BookingChannel::class),
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(BookingStatus::class),
                SelectFilter::make('package_id')
                    ->label('الباقة')
                    ->relationship('package', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('trip_id')
                    ->label('الرحلة')
                    ->relationship('trip', 'name')
                    ->searchable()
                    ->preload(),
                DateRangeFilter::make('due_date'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                RecordPaymentAction::make(),
                PrintReceiptAction::make(),
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
