<?php

namespace App\Filament\Resources\JournalEntries\Schemas;

use App\Enums\JournalEntrySourceType;
use App\Enums\JournalEntryStatus;
use App\Models\JournalEntry as JournalEntryModel;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class JournalEntryForm
{
    protected static function isReadOnly(?JournalEntryModel $record): bool
    {
        return $record && $record->status !== JournalEntryStatus::DRAFT;
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات القيد')
                    ->schema([
                        TextInput::make('number')
                            ->label('رقم القيد')
                            ->hiddenOn('create')
                            ->readOnly()
                            ->dehydrated(false),
                        Select::make('fiscal_year_id')
                            ->label('السنة المالية')
                            ->relationship('fiscalYear', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        DatePicker::make('entry_date')
                            ->label('تاريخ القيد')
                            ->default(now())
                            ->required(),
                        Select::make('status')
                            ->label('الحالة')
                            ->options(JournalEntryStatus::class)
                            ->default('draft')
                            ->required()
                            ->live(),
                        Select::make('source_type')
                            ->label('نوع المصدر')
                            ->options(JournalEntrySourceType::class)
                            ->default('manual')
                            ->required()
                            ->live(),
                        TextInput::make('source_id')
                            ->label('رقم المصدر')
                            ->numeric()
                            ->visible(fn (Get $get): bool => $get('source_type') !== JournalEntrySourceType::MANUAL),
                        TextInput::make('reference')
                            ->label('المرجع'),
                        Select::make('posted_by')
                            ->label('راجعّه')
                            ->relationship('postedBy', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn (Get $get): bool => $get('status') === JournalEntryStatus::POSTED),
                    ])
                    ->disabled(fn (?JournalEntryModel $record): bool => static::isReadOnly($record))
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('التفاصيل')
                    ->schema([
                        Textarea::make('description')
                            ->label('الوصف')
                            ->required()
                            ->columnSpanFull(),
                        Textarea::make('notes')
                            ->label('ملاحظات')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('المرفقات')
                    ->schema([
                        FileUpload::make('attachment')
                            ->label('المرفق')
                            ->directory('journal-entries')
                            ->visibility('public'),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),
                Hidden::make('created_by')
                    ->default(fn () => auth('web')->id()),
            ]);
    }
}
