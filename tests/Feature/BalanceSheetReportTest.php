<?php

use App\Enums\AccountClass;
use App\Enums\JournalEntryStatus;
use App\Filament\Pages\Reports\Widgets\BalanceSheetSummaryWidget;
use App\Models\Account;
use App\Models\AccountOpeningBalance;
use App\Models\FiscalYear;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createAccount(array $overrides = []): Account
{
    return Account::create(array_merge([
        'code' => fake()->unique()->numerify('####'),
        'name' => fake()->word(),
        'class' => AccountClass::ASSETS,
        'type' => 'detail',
        'normal_balance' => 'debit',
        'is_active' => true,
        'level' => 1,
    ], $overrides));
}

function createFiscalYear(array $overrides = []): FiscalYear
{
    return FiscalYear::create(array_merge([
        'name' => '2026',
        'starts_at' => '2026-01-01',
        'ends_at' => '2026-12-31',
        'status' => 'open',
    ], $overrides));
}

function createJournalEntry(FiscalYear $fy, User $user, array $overrides = []): JournalEntry
{
    return JournalEntry::create(array_merge([
        'number' => fake()->unique()->numerify('JE-#####'),
        'fiscal_year_id' => $fy->id,
        'entry_date' => now()->toDateString(),
        'status' => JournalEntryStatus::DRAFT,
        'source_type' => 'manual',
        'description' => fake()->sentence(),
        'created_by' => $user->id,
    ], $overrides));
}

it('computes correct balance sheet summary', function () {
    $user = User::factory()->create();
    $fy = createFiscalYear();

    $cash = createAccount(['class' => AccountClass::ASSETS, 'code' => '1110', 'name' => 'الصندوق', 'normal_balance' => 'debit']);
    $receivable = createAccount(['class' => AccountClass::ASSETS, 'code' => '1120', 'name' => 'المدينون', 'normal_balance' => 'debit']);
    $payable = createAccount(['class' => AccountClass::LIABILITIES, 'code' => '2110', 'name' => 'الدائنون', 'normal_balance' => 'credit']);
    $capital = createAccount(['class' => AccountClass::EQUITY, 'code' => '3110', 'name' => 'رأس المال', 'normal_balance' => 'credit']);

    AccountOpeningBalance::create([
        'account_id' => $cash->id,
        'fiscal_year_id' => $fy->id,
        'debit_amount' => 10000,
        'credit_amount' => 0,
    ]);

    AccountOpeningBalance::create([
        'account_id' => $capital->id,
        'fiscal_year_id' => $fy->id,
        'debit_amount' => 0,
        'credit_amount' => 10000,
    ]);

    $entry = createJournalEntry($fy, $user, [
        'status' => JournalEntryStatus::POSTED,
        'posted_by' => $user->id,
        'posted_at' => now(),
    ]);

    JournalLine::create([
        'journal_entry_id' => $entry->id,
        'account_id' => $cash->id,
        'debit_amount' => 40000,
        'credit_amount' => 0,
    ]);

    JournalLine::create([
        'journal_entry_id' => $entry->id,
        'account_id' => $receivable->id,
        'debit_amount' => 15000,
        'credit_amount' => 0,
    ]);

    JournalLine::create([
        'journal_entry_id' => $entry->id,
        'account_id' => $payable->id,
        'debit_amount' => 0,
        'credit_amount' => 20000,
    ]);

    JournalLine::create([
        'journal_entry_id' => $entry->id,
        'account_id' => $capital->id,
        'debit_amount' => 0,
        'credit_amount' => 35000,
    ]);

    $summary = BalanceSheetSummaryWidget::computeSummary($fy->id, null, null);

    expect($summary['total_assets'])->toBe(65000);
    expect($summary['total_liabilities'])->toBe(20000);
    expect($summary['total_equity'])->toBe(45000);
    expect($summary['net_income'])->toBe(0);
    expect($summary['difference'])->toBe(0);
    expect($summary['is_balanced'])->toBeTrue();
});

it('includes opening balances in balance sheet calculation', function () {
    $user = User::factory()->create();
    $fy = createFiscalYear();

    $cash = createAccount(['class' => AccountClass::ASSETS, 'code' => '1110', 'name' => 'الصندوق', 'normal_balance' => 'debit']);
    $capital = createAccount(['class' => AccountClass::EQUITY, 'code' => '3110', 'name' => 'رأس المال', 'normal_balance' => 'credit']);

    AccountOpeningBalance::create([
        'account_id' => $cash->id,
        'fiscal_year_id' => $fy->id,
        'debit_amount' => 25000,
        'credit_amount' => 0,
    ]);

    AccountOpeningBalance::create([
        'account_id' => $capital->id,
        'fiscal_year_id' => $fy->id,
        'debit_amount' => 0,
        'credit_amount' => 25000,
    ]);

    $summary = BalanceSheetSummaryWidget::computeSummary($fy->id, null, null);

    expect($summary['total_assets'])->toBe(25000);
    expect($summary['total_equity'])->toBe(25000);
    expect($summary['difference'])->toBe(0);
    expect($summary['is_balanced'])->toBeTrue();
});

it('filters by date range correctly', function () {
    $user = User::factory()->create();
    $fy = createFiscalYear();

    $cash = createAccount(['class' => AccountClass::ASSETS, 'code' => '1110', 'name' => 'الصندوق', 'normal_balance' => 'debit']);
    $capital = createAccount(['class' => AccountClass::EQUITY, 'code' => '3110', 'name' => 'رأس المال', 'normal_balance' => 'credit']);

    $entry1 = createJournalEntry($fy, $user, [
        'entry_date' => '2026-01-15',
        'status' => JournalEntryStatus::POSTED,
        'posted_by' => $user->id,
        'posted_at' => now(),
    ]);
    JournalLine::create([
        'journal_entry_id' => $entry1->id,
        'account_id' => $cash->id,
        'debit_amount' => 10000,
        'credit_amount' => 0,
    ]);
    JournalLine::create([
        'journal_entry_id' => $entry1->id,
        'account_id' => $capital->id,
        'debit_amount' => 0,
        'credit_amount' => 10000,
    ]);

    $entry2 = createJournalEntry($fy, $user, [
        'entry_date' => '2026-06-15',
        'status' => JournalEntryStatus::POSTED,
        'posted_by' => $user->id,
        'posted_at' => now(),
    ]);
    JournalLine::create([
        'journal_entry_id' => $entry2->id,
        'account_id' => $cash->id,
        'debit_amount' => 20000,
        'credit_amount' => 0,
    ]);
    JournalLine::create([
        'journal_entry_id' => $entry2->id,
        'account_id' => $capital->id,
        'debit_amount' => 0,
        'credit_amount' => 20000,
    ]);

    $summaryAll = BalanceSheetSummaryWidget::computeSummary($fy->id, null, null);
    expect($summaryAll['total_assets'])->toBe(30000);

    $summaryH1 = BalanceSheetSummaryWidget::computeSummary($fy->id, '2026-01-01', '2026-06-30');
    expect($summaryH1['total_assets'])->toBe(30000);

    $summaryQ1 = BalanceSheetSummaryWidget::computeSummary($fy->id, '2026-01-01', '2026-03-31');
    expect($summaryQ1['total_assets'])->toBe(10000);
});

it('only includes posted journal entries', function () {
    $user = User::factory()->create();
    $fy = createFiscalYear();

    $cash = createAccount(['class' => AccountClass::ASSETS, 'code' => '1110', 'name' => 'الصندوق', 'normal_balance' => 'debit']);
    $capital = createAccount(['class' => AccountClass::EQUITY, 'code' => '3110', 'name' => 'رأس المال', 'normal_balance' => 'credit']);

    $postedEntry = createJournalEntry($fy, $user, [
        'status' => JournalEntryStatus::POSTED,
        'posted_by' => $user->id,
        'posted_at' => now(),
    ]);
    JournalLine::create([
        'journal_entry_id' => $postedEntry->id,
        'account_id' => $cash->id,
        'debit_amount' => 10000,
        'credit_amount' => 0,
    ]);
    JournalLine::create([
        'journal_entry_id' => $postedEntry->id,
        'account_id' => $capital->id,
        'debit_amount' => 0,
        'credit_amount' => 10000,
    ]);

    $draftEntry = createJournalEntry($fy, $user, ['status' => JournalEntryStatus::DRAFT]);
    JournalLine::create([
        'journal_entry_id' => $draftEntry->id,
        'account_id' => $cash->id,
        'debit_amount' => 50000,
        'credit_amount' => 0,
    ]);
    JournalLine::create([
        'journal_entry_id' => $draftEntry->id,
        'account_id' => $capital->id,
        'debit_amount' => 0,
        'credit_amount' => 50000,
    ]);

    $summary = BalanceSheetSummaryWidget::computeSummary($fy->id, null, null);
    expect($summary['total_assets'])->toBe(10000);
});

it('excludes soft-deleted journal entries', function () {
    $user = User::factory()->create();
    $fy = createFiscalYear();

    $cash = createAccount(['class' => AccountClass::ASSETS, 'code' => '1110', 'name' => 'الصندوق', 'normal_balance' => 'debit']);
    $capital = createAccount(['class' => AccountClass::EQUITY, 'code' => '3110', 'name' => 'رأس المال', 'normal_balance' => 'credit']);

    $entry = createJournalEntry($fy, $user, [
        'status' => JournalEntryStatus::POSTED,
        'posted_by' => $user->id,
        'posted_at' => now(),
    ]);
    JournalLine::create([
        'journal_entry_id' => $entry->id,
        'account_id' => $cash->id,
        'debit_amount' => 10000,
        'credit_amount' => 0,
    ]);
    JournalLine::create([
        'journal_entry_id' => $entry->id,
        'account_id' => $capital->id,
        'debit_amount' => 0,
        'credit_amount' => 10000,
    ]);

    $draftEntry = createJournalEntry($fy, $user, ['status' => JournalEntryStatus::DRAFT]);
    JournalLine::create([
        'journal_entry_id' => $draftEntry->id,
        'account_id' => $cash->id,
        'debit_amount' => 50000,
        'credit_amount' => 0,
    ]);
    JournalLine::create([
        'journal_entry_id' => $draftEntry->id,
        'account_id' => $capital->id,
        'debit_amount' => 0,
        'credit_amount' => 50000,
    ]);
    $draftEntry->delete();

    $summary = BalanceSheetSummaryWidget::computeSummary($fy->id, null, null);
    expect($summary['total_assets'])->toBe(10000);
});
