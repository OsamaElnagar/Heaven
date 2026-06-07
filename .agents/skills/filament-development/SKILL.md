---
name: filament-development
description: "Comprehensive Filament development skill covering forms, tables, resources, relation managers, custom pages, custom actions, and PDF generation. Invoke for any Filament PHP work."
license: MIT
metadata:
  author: user
---

# Filament Development — Complete Guide

---

## 1. Form Component Wrapping

Always wrap Filament form components with a Section to maintain consistent styling:

```php
use Filament\Schemas\Components\Section;

Section::make('')
    ->components([
        TextInput::make('name')->required(),
        TextInput::make('email')->email()->required(),
    ])
    ->columns(2)
    ->columnSpanFull(),
```

### Rules

- Always wrap form fields in a Section
- Use `columns(2)` for side-by-side fields
- Use `columnSpanFull()` to make the section span full width
- Keep related fields together in the same Section

---

## 2. Resource Static Properties

Every Filament Resource MUST use these property type declarations exactly:

```php
protected static ?string $model = Model::class;
protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
protected static \UnitEnum|string|null $navigationGroup = '...';
protected static ?string $navigationLabel = '...';
protected static ?string $recordTitleAttribute = 'code';
protected static ?string $modelLabel = '...';
protected static ?string $pluralModelLabel = '...';
```

### Global Search Methods

```php
public static function getGloballySearchableAttributes(): array
{
    return ['code', 'name', 'phone'];
}

public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
{
    return $record->name.' - '.$record->code;
}

public static function getGlobalSearchResultActions(Model $record): array
{
    return [
        Action::make('edit')
            ->url(static::getUrl('edit', ['record' => $record])),
    ];
}
```

### Soft Deletes

For models using SoftDeletes, always include:

```php
public static function getRecordRouteBindingEloquentQuery(): Builder
{
    return parent::getRecordRouteBindingEloquentQuery()
        ->withoutGlobalScopes([SoftDeletingScope::class]);
}
```

---

## 3. Page Patterns

### List Page

```php
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListModels extends ListRecords
{
    protected static string $resource = ModelResource::class;
    protected static ?string $title = 'قائمة ...';

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
```

### Create Page

```php
use Filament\Resources\Pages\CreateRecord;

class CreateModel extends CreateRecord
{
    protected static string $resource = ModelResource::class;
    protected static ?string $title = 'إنشاء ...';
}
```

### Edit Page (soft-deletes)

```php
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditModel extends EditRecord
{
    protected static string $resource = ModelResource::class;
    protected static ?string $title = 'تعديل ...';

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make(), ForceDeleteAction::make(), RestoreAction::make()];
    }
}
```

### Edit Page (no soft-deletes)

```php
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditModel extends EditRecord
{
    protected static string $resource = ModelResource::class;
    protected static ?string $title = 'تعديل ...';

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
```

### View Page (soft-deletes)

```php
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\ViewRecord;

class ViewModel extends ViewRecord
{
    protected static string $resource = ModelResource::class;
    protected static ?string $title = 'عرض ...';

    protected function getHeaderActions(): array
    {
        return [EditAction::make(), DeleteAction::make(), ForceDeleteAction::make(), RestoreAction::make()];
    }
}
```

---

## 4. Table Pattern

```php
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;

class ModelsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->searchable(),
                TextColumn::make('name')->searchable(),
                IconColumn::make('is_active')->boolean(),
            ])
            ->filters([
                SelectFilter::make('is_active')->options(['1' => 'Active', '0' => 'Inactive']),
                TrashedFilter::make(), // only for soft-deletes
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(), // only soft-deletes
                    RestoreBulkAction::make(),      // only soft-deletes
                ]),
            ]);
    }
}
```

### DateRangeFilter

Always add DateRangeFilter to tables with date columns:

```php
use App\Filament\Components\Filters\DateRangeFilter;

// In filters array:
DateRangeFilter::make('voucher_date'),   // column name = field name
DateRangeFilter::make('invoice_date'),   // column name = field name
DateRangeFilter::make('starts_at'),      // uses column 'starts_at'
```

Usage: `DateRangeFilter::make(string $name, ?string $column = null, string $label = 'التاريخ')`

---

## 5. Enum Fields in Forms and Filters

Pass enum class directly — Filament auto-converts:

```php
// Form
Select::make('status')->options(FiscalYearStatus::class)->default(FiscalYearStatus::OPEN),

// Table filter
SelectFilter::make('status')->options(FiscalYearStatus::class),
```

---

## 6. Relation Manager Patterns

### Basic RM (reusing related Resource form/table)

```php
use App\Filament\Resources\PurchaseInvoices\PurchaseInvoiceResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class PurchaseInvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'purchaseInvoices';
    protected static ?string $title = 'فواتير المشتريات';

    public function schema(Schema $schema): Schema
    {
        return PurchaseInvoiceResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return PurchaseInvoiceResource::table($table);
    }
}
```

### Hiding parent FK when form is reused in RM

When you reuse a Resource's form inside a RelationManager, hide the parent foreign key — it's already known from the relationship context:

```php
// In PurchaseInvoiceForm — the form is reused in SupplierResource's RM
Select::make('supplier_id')
    ->relationship('supplier', 'name')
    ->required()
    ->hiddenOn(\App\Filament\Resources\Suppliers\RelationManagers\PurchaseInvoicesRelationManager::class),

Select::make('subcontractor_id')
    ->relationship('subcontractor', 'name')
    ->hiddenOn(\App\Filament\Resources\Subcontractors\RelationManagers\PurchaseInvoicesRelationManager::class),
```

```php
// In PaymentVoucherForm — reused in Safe, BankAccount, Supplier, etc. RMs
Select::make('safe_id')
    ->relationship('safe', 'name')
    ->hiddenOn(\App\Filament\Resources\Safes\RelationManagers\PaymentVouchersRelationManager::class),

Select::make('bank_account_id')
    ->relationship('bankAccount', 'name')
    ->hiddenOn(\App\Filament\Resources\BankAccounts\RelationManagers\PaymentVouchersRelationManager::class),

Select::make('supplier_id')
    ->relationship('supplier', 'name')
    ->hiddenOn(\App\Filament\Resources\Suppliers\RelationManagers\PaymentVouchersRelationManager::class),
```

Same applies to table columns — hide the parent column in the RM's table context:

```php
TextColumn::make('supplier.name')
    ->hiddenOn(\App\Filament\Resources\Suppliers\RelationManagers\PurchaseInvoicesRelationManager::class),
```

### Inline RM (no separate resource)

```php
use Filament\Resources\RelationManagers\RelationManager;

class ProjectAssignmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'projectEquipment';
    protected static ?string $title = 'تخصيصات المشاريع';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('project_id')->relationship('project', 'name'),
            DatePicker::make('assigned_date'),
            DatePicker::make('returned_date'),
            TextInput::make('charged_amount')->numeric(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('project.name'),
                TextColumn::make('assigned_date')->date(),
                TextColumn::make('returned_date')->date(),
                TextColumn::make('charged_amount')->numeric(),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
```

### Read-only RM (no create/edit)

```php
class InventoryTransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'inventoryTransactions';
    protected static ?string $title = 'حركات المخزون';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number'),
                TextColumn::make('type')->badge(),
                TextColumn::make('quantity')->numeric(),
            ]);
        // No headerActions, no recordActions
    }
}
```

### RM Localization Properties

```php
protected static string $relationship = 'feeds';
protected static ?string $title = 'صرف الأعلاف اليومى';
protected static ?string $modelLabel = 'صرف علف';
protected static ?string $pluralModelLabel = 'صرف الأعلاف';
```

---

## 7. Custom Actions (CA)

Custom actions extend `Filament\Actions\Action` (page-level) or `Filament\Actions\CreateAction` (for bulk creation).

### Pattern: Page-level action with form & slideOver

```php
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class GiveAdvanceAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'giveAdvance';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('صرف سُلفة')
            ->icon('heroicon-o-currency-dollar')
            ->color('warning')
            ->form([
                DatePicker::make('date')->label('التاريخ')->default(now())->required(),
                TextInput::make('amount')->label('المبلغ')->numeric()->required(),
                Select::make('treasury_account_id')
                    ->label('الخزينة الصادر منها')
                    ->options(fn () => Account::where('is_treasury', true)->pluck('name', 'id'))
                    ->required(),
                Textarea::make('reason')->label('السبب / البيان'),
            ])
            ->action(function (array $data, Model $record) {
                // Create record
                RelatedModel::create([...]);

                Notification::make()
                    ->title('تم التسجيل بنجاح')
                    ->success()
                    ->send();
            })
            ->slideOver();
    }
}
```

### Pattern: Bulk creation via Repeater in modal

```php
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Repeater;

class BulkTransactionsAction extends CreateAction
{
    public static function getDefaultName(): ?string
    {
        return 'bulkTransactions';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('إضافة معاملات متعددة')
            ->modalHeading('إضافة معاملات متعددة')
            ->modalWidth('7xl')
            ->schema([
                Repeater::make('transactions')
                    ->label('المعاملات')
                    ->schema(fn (Schema $schema) => SomeForm::configure($schema->livewire($this->getLivewire())))
                    ->minItems(1)
                    ->reorderable()
                    ->columns(3)
                    ->columnSpanFull(),
            ])
            ->action(function (array $data): void {
                $livewire = $this->getLivewire();

                foreach ($data['transactions'] ?? [] as $transactionData) {
                    $livewire->getRelationship()->create(array_merge($transactionData, [
                        'recorded_by' => Auth::id(),
                    ]));
                }
            });
    }
}
```

**Key technique:** When reusing a form class inside a Repeater within an action, call `->livewire($this->getLivewire())` on the schema to inject the Livewire context:

```php
Repeater::make('transactions')
    ->schema(fn (Schema $schema) => MyForm::configure($schema->livewire($this->getLivewire())))
```

### Where to place custom actions

- Page-level actions: `app/Filament/Resources/{Resource}/Actions/`
- Add to resource via `getHeaderActions()` on List/Edit/View pages
- Or register globally on the Resource via static methods

---

## 8. Custom Pages (CP)

Custom pages extend `Filament\Pages\Page`. For pages with forms, use `InteractsWithForms`. For pages with tables, use `InteractsWithTable`.

### Custom Page with Table + Record

```php
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

class StatementOfAccount extends Page implements HasTable
{
    use InteractsWithRecord;
    use InteractsWithTable;

    protected static string $resource = TraderResource::class;
    protected string $view = 'filament.resources.traders.pages.statement-of-account';
    protected static ?string $title = 'كشف حساب';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    public function getBreadcrumbs(): array
    {
        $resource = static::getResource();
        return [
            $resource::getUrl('index') => $resource::getBreadcrumb(),
            '#' => static::$title,
        ];
    }

    public function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            EditAction::make(),
            ActionGroup::make([
                Action::make('exportPdf')->label('تصدير PDF')->action(fn () => ...),
            ])->label('الإجراءات')->button(),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(JournalLine::query()->where(...))
            ->columns([
                TextColumn::make('journalEntry.date')->label('التاريخ')->date()->sortable(),
                TextColumn::make('debit')->label('مدين')->money('EGP'),
                TextColumn::make('credit')->label('دائن')->money('EGP'),
            ])
            ->defaultSort('journalEntry.date', 'desc')
            ->filters([
                DateRangeFilter::make('date')->query(function (Builder $query, array $data) {
                    return $query
                        ->when($data['date_from'], fn ($q, $d) => $q->whereHas('journalEntry', fn ($j) => $j->whereDate('date', '>=', $d)))
                        ->when($data['date_to'], fn ($q, $d) => $q->whereHas('journalEntry', fn ($j) => $j->whereDate('date', '<=', $d)));
                }),
            ]);
    }
}
```

### Blade template for custom page

```blade
<x-filament-panels::page>
    <div class="space-y-4">
        <x-filament::callout icon="heroicon-o-document-text" color="gray">
            <x-slot name="heading">عنوان</x-slot>
            <x-slot name="description">وصف</x-slot>
        </x-filament::callout>

        {{ $this->table }}
    </div>
</x-filament-panels::page>
```

### Registering custom pages in a Resource

```php
public static function getPages(): array
{
    return [
        'index' => ListModels::route('/'),
        'create' => CreateModel::route('/create'),
        'edit' => EditModel::route('/{record}/edit'),
        'statement' => StatementOfAccount::route('/{record}/statement'),
    ];
}
```

### Header Widgets on Custom Pages

```php
protected function getHeaderWidgets(): array
{
    return [TraderStatsWidget::class];
}
```

---

## 9. PDF Generation

Use `mccarlosen/laravel-mpdf` for PDF generation with Blade templates.

### PdfService Pattern

```php
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;
use Mccarlosen\LaravelMpdf\LaravelMpdf;

class PdfService
{
    public function generateStatementPdf(
        string $type,
        string $entityName,
        array $statement,
        array $entries
    ): LaravelMpdf {
        return PDF::loadView('pdf.statement', [
            'type' => $type,
            'entityName' => $entityName,
            'statement' => $statement,
            'entries' => $entries,
            'generatedAt' => now()->format('Y-m-d h:i A'),
            'storeName' => config('app.name'),
        ]);
    }
}
```

### Downloading PDF from an Action

```php
Action::make('exportPdf')
    ->label('تصدير PDF')
    ->icon('heroicon-o-document-arrow-down')
    ->action(function () {
        $pdf = (new PdfService)->generateStatementPdf('type', $name, $data, $entries);

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'filename-'.now()->format('Y-m-d').'.pdf'
        );
    }),
```

### PDF Blade Template (Arabic RTL)

```blade
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $statement['title'] }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; }
        .header { text-align: center; border-bottom: 3px solid #1a5f7a; }
        .entries-table { width: 100%; border-collapse: collapse; }
        .entries-table th { background: #3498db; color: white; }
        .entries-table tr:nth-child(even) { background-color: #f0f8ff; }
        .summary-table { width: 280px; margin-right: 0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $storeName }}</h1>
        <h2>{{ $statement['title'] }} - {{ $entityName }}</h2>
    </div>
    <table class="entries-table">
        <thead><tr><th>التاريخ</th><th>البيان</th><th>مدين</th><th>دائن</th></tr></thead>
        <tbody>
        @foreach($entries as $entry)
            <tr>
                <td>{{ $entry['date'] }}</td>
                <td>{{ $entry['description'] }}</td>
                <td>{{ number_format($entry['debit']) }}</td>
                <td>{{ number_format($entry['credit']) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <p class="footer">تم إنشاء هذا التقرير في {{ $generatedAt }}</p>
</body>
</html>
```

---

## 10. Advanced Form Patterns

### Detecting RelationManager context

```php
$livewire = null;
try {
    $livewire = $schema->getLivewire();
} catch (\TypeError) {
    // livewire not yet set (e.g., inside a Bulk Action)
}

$ownerRecord = ($livewire instanceof RelationManager) ? $livewire->getOwnerRecord() : null;
$isRelated = $ownerRecord instanceof SomeModel;
```

### Conditional defaults based on owner record

```php
Select::make('farm_id')
    ->default(function () use ($isFarmManager, $ownerRecord) {
        if ($isFarmManager) return $ownerRecord->getKey();
        return null;
    })
    ->disabled($isFarmManager),
```

### Reactive field updates with Set/Get

```php
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

Select::make('petty_cash_id')
    ->live()
    ->afterStateUpdated(fn (Set $set, Get $get, $state) => self::updateFields($set, $get, $state)),

private static function updateFields(Set $set, Get $get, $state): void
{
    if (! $state) {
        $set('current_balance', null);
        return;
    }
    $model = Model::find($state);
    $set('current_balance', $model->balance);
}
```

### Using Infolist TextEntry inside forms

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('current_balance')
    ->label('الرصيد الحالي')
    ->placeholder('اختر عنصراً'),
```

### Caching options in Select

```php
Select::make('petty_cash_id')
    ->options(function () {
        return Cache::remember('petty_cashes_list', now()->addDay(), fn () =>
            PettyCash::pluck('name', 'id')->toArray()
        );
    })
    ->searchable()
    ->preload(),
```

### Conditional field visibility

```php
Select::make('expense_category_id')
    ->visible(fn (Get $get) => $get('direction') === 'out')
    ->required(fn (Get $get) => $get('direction') === 'out'),

Select::make('employee_id')
    ->visible(function (Get $get) {
        if ($get('direction') !== 'out') return false;
        $catId = $get('expense_category_id');
        if (! $catId) return false;
        return ExpenseCategory::find($catId)?->code === 'WORKER_SALARY';
    }),
```

### Dynamic relationship filters

```php
Select::make('batch_id')
    ->relationship('batch', 'batch_code', modifyQueryUsing: function ($query, Get $get) {
        $farmId = $get('farm_id');
        if ($farmId) {
            return $query->where('farm_id', $farmId)->where('is_cycle_closed', false);
        }
        return $query->whereRaw('1 = 0');
    }),
```

---

## 11. Reusable Components

### DateRangeFilter (table filter)

Location: `app/Filament/Components/Filters/DateRangeFilter.php`

```php
// In any table ->filters([]):
DateRangeFilter::make('voucher_date'),          // field name = column name
DateRangeFilter::make('date', 'entry_date'),    // custom column
DateRangeFilter::make('date', label: 'الفترة'), // custom label

// With custom query (for nested relations):
DateRangeFilter::make('date')->query(function (Builder $query, array $data) {
    return $query
        ->when($data['date_from'], fn ($q, $d) => $q->whereDate('date', '>=', $d))
        ->when($data['date_to'], fn ($q, $d) => $q->whereDate('date', '<=', $d));
}),
```

---

## 12. Full Resource File Structure

```
app/Filament/Resources/{PluralName}/
├── {SingularName}Resource.php          # Resource class

├── Schemas/
│   └── {SingularName}Form.php          # Form schema

├── Tables/
│   └── {PluralName}Table.php           # Table definition

├── Pages/
│   ├── List{PluralName}.php            # List page

│   ├── Create{SingularName}.php        # Create page

│   ├── Edit{SingularName}.php          # Edit page

│   └── View{SingularName}.php          # View page (optional)

├── RelationManagers/                    # RM classes

│   └── RelatedItemsRelationManager.php
└── Actions/                             # Custom actions

    └── SomeAction.php
```

---

## 13. Key Conventions Summary

| Convention       | Rule                                                                     |
| ---------------- | ------------------------------------------------------------------------ |
| Form wrapping    | `Section::make('')->components([...])->columns(2)->columnSpanFull()`   |
| Enums            | Pass class directly:`->options(EnumClass::class)`                      |
| Navigation group | `protected static \UnitEnum\|string\|null $navigationGroup`              |
| Navigation icon  | `protected static string\|BackedEnum\|null $navigationIcon`              |
| Soft deletes     | Remove `SoftDeletingScope` in query, add `TrashedFilter`             |
| Arabic labels    | All model labels, nav labels, titles in Arabic                           |
| RM reuse         | Call `OtherResource::form($schema)` / `OtherResource::table($table)` |
| Read-only RM     | No `headerActions`, no `recordActions`                               |
| Date filters     | Use `DateRangeFilter::make('column')` in all relevant tables           |
| PDF              | Use `laravel-mpdf` with Blade views                                    |
| Custom pages     | Extend `Page`, use `InteractsWithRecord` + `InteractsWithTable`    |
| Custom actions   | Extend `Action` or `CreateAction`, override `setUp()`              |
