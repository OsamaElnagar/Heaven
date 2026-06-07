<?php

namespace App\Filament\Resources\FiscalYears\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DocumentSequencesRelationManager extends RelationManager
{
    protected static string $relationship = 'documentSequences';

    protected static ?string $title = 'تسلسل المستندات';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('document_type')
                    ->label('نوع المستند')
                    ->required()
                    ->maxLength(50),
                TextInput::make('prefix')
                    ->label('البادئة')
                    ->required()
                    ->maxLength(50),
                TextInput::make('last_number')
                    ->label('آخر رقم')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('padding')
                    ->label('عدد الخانات')
                    ->required()
                    ->numeric()
                    ->default(5),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('document_type')
            ->columns([
                TextColumn::make('document_type')
                    ->label('نوع المستند')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('prefix')
                    ->label('البادئة')
                    ->searchable(),
                TextColumn::make('last_number')
                    ->label('آخر رقم')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('padding')
                    ->label('عدد الخانات')
                    ->numeric()
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()->label('إضافة تسلسل'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
