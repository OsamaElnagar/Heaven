<?php

namespace App\Filament\Resources\Attendances\Tables;

use App\Enums\AttendanceStatus;
use App\Filament\Components\Filters\DateRangeFilter;
use App\Filament\Resources\Employees\EmployeeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AttendancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.name')
                    ->label('الموظف')
                    ->url(fn ($record) => $record->employee ? EmployeeResource::getUrl('edit', ['record' => $record->employee]) : null, true)
                    ->color('info')
                    ->icon(Heroicon::ArrowUpRight)
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('date')
                    ->label('التاريخ')
                    ->date()
                    ->sortable(),
                TextColumn::make('check_in')
                    ->label('وقت الحضور')
                    ->time('h:i a'),
                TextColumn::make('check_out')
                    ->label('وقت الانصراف')
                    ->time('h:i a'),
                TextColumn::make('status')
                    ->label('حالة الحضور')
                    ->badge()
                    ->sortable(),
                TextColumn::make('overtime_hours')
                    ->label('ساعات إضافية')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('notes')
                    ->label('ملاحظات')
                    ->limit(30),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                SelectFilter::make('employee_id')
                    ->relationship('employee', 'name')
                    ->label('الموظف')
                    ->searchable()
                    ->preload(),
                DateRangeFilter::make('date', 'date')
                    ->label('التاريخ'),
                SelectFilter::make('status')
                    ->label('حالة الحضور')
                    ->options(AttendanceStatus::class),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}
