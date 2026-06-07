<?php

namespace App\Filament\Resources\JournalEntries\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LinesRelationManager extends RelationManager
{
    protected static string $relationship = 'lines';

    protected static ?string $title = 'بنود القيد';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('account_id')
                    ->label('الحساب')
                    ->relationship('account', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('debit_amount')
                    ->label('مدين')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->minValue(0),
                TextInput::make('credit_amount')
                    ->label('دائن')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->minValue(0),
                TextInput::make('description')
                    ->label('البيان'),
                TextInput::make('sort_order')
                    ->label('الترتيب')
                    ->required()
                    ->numeric()
                    ->default(0),
                Select::make('client_id')
                    ->label('العميل')
                    ->relationship('client', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('supplier_id')
                    ->label('المورد')
                    ->relationship('supplier', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('employee_id')
                    ->label('الموظف')
                    ->relationship('employee', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('safe_id')
                    ->label('الخزينة')
                    ->relationship('safe', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('bank_account_id')
                    ->label('الحساب البنكي')
                    ->relationship('bankAccount', 'bank_name')
                    ->searchable()
                    ->preload(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('account_id')
            ->columns([
                TextColumn::make('sort_order')
                    ->label('#')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('account.code')
                    ->label('كود الحساب')
                    ->searchable(),
                TextColumn::make('account.name')
                    ->label('اسم الحساب')
                    ->searchable(),
                TextColumn::make('description')
                    ->label('البيان')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('debit_amount')
                    ->label('مدين')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('credit_amount')
                    ->label('دائن')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('client.name')
                    ->label('العميل')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('supplier.name')
                    ->label('المورد')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('employee.name')
                    ->label('الموظف')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('safe.name')
                    ->label('الخزينة')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('bankAccount.bank_name')
                    ->label('البنك')
                    ->searchable()
                    ->toggleable(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()->label('إضافة بند'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
