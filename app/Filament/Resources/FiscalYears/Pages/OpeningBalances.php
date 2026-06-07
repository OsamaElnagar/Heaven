<?php

namespace App\Filament\Resources\FiscalYears\Pages;

use App\Enums\JournalEntrySourceType;
use App\Enums\JournalEntryStatus;
use App\Filament\Resources\FiscalYears\FiscalYearResource;
use App\Models\AccountOpeningBalance;
use App\Models\JournalEntry;
use App\Services\JournalService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class OpeningBalances extends Page implements HasTable
{
    use InteractsWithRecord;
    use InteractsWithTable;

    protected static string $resource = FiscalYearResource::class;

    protected string $view = 'filament.pages.statement';

    protected static ?string $title = 'الأرصدة الافتتاحية';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    public function getBreadcrumbs(): array
    {
        return [
            FiscalYearResource::getUrl('index') => FiscalYearResource::getBreadcrumb(),
            '#' => static::$title,
        ];
    }

    public function getHeaderActions(): array
    {
        $postedEntry = $this->getPostedEntry();

        return [
            Action::make('postToJournal')
                ->label($postedEntry ? 'تم الترحيل' : 'ترحيل إلى اليومية')
                ->icon($postedEntry ? 'heroicon-o-check-circle' : 'heroicon-o-arrow-up-circle')
                ->color($postedEntry ? 'gray' : 'primary')
                ->disabled($postedEntry !== null)
                ->requiresConfirmation()
                ->modalHeading('ترحيل الأرصدة الافتتاحية')
                ->modalDescription('هل أنت متأكد من ترحيل الأرصدة الافتتاحية إلى دفتر اليومية؟ يجب أن يكون مجموع المدين مساوياً لمجموع الدائن.')
                ->modalSubmitActionLabel('نعم، ترحيل')
                ->action(fn () => $this->postToJournal()),
        ];
    }

    protected function getPostedEntry(): ?JournalEntry
    {
        return JournalEntry::where('source_type', JournalEntrySourceType::OPENING_BALANCE)
            ->where('source_id', $this->record->id)
            ->where('status', JournalEntryStatus::POSTED)
            ->first();
    }

    protected function postToJournal(): void
    {
        try {
            $entry = app(JournalService::class)->postOpeningBalances($this->record->id);

            Notification::make()
                ->title('تم ترحيل الأرصدة الافتتاحية بنجاح')
                ->body('رقم القيد: '.$entry->number)
                ->success()
                ->send();
        } catch (\RuntimeException $e) {
            Notification::make()
                ->title($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function getTitle(): string
    {
        $name = $this->record->name ?? '';

        return static::$title.' - '.$name;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                AccountOpeningBalance::query()
                    ->where('fiscal_year_id', $this->record->id)
                    ->with('account')
            )
            ->columns([
                TextColumn::make('account.code')
                    ->label('كود الحساب')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('account.name')
                    ->label('اسم الحساب')
                    ->searchable(),
                TextColumn::make('debit_amount')
                    ->label('مدين')
                    ->numeric()
                    ->color('danger')
                    ->summarize(Sum::make()->label('إجمالي المدين')),
                TextColumn::make('credit_amount')
                    ->label('دائن')
                    ->numeric()
                    ->color('success')
                    ->summarize(Sum::make()->label('إجمالي الدائن')),
                TextColumn::make('net')
                    ->label('الصافي')
                    ->getStateUsing(fn (AccountOpeningBalance $record) => $record->getNetAmount())
                    ->color(fn (AccountOpeningBalance $record) => $record->getNetAmount() >= 0 ? 'danger' : 'success'),
            ])
            ->defaultSort('account.code');
    }
}
