<?php

use App\Enums\AccountClass;
use App\Enums\AccountNormalBalance;
use App\Enums\AccountType;
use App\Enums\FiscalYearStatus;
use App\Enums\SupplierType;
use App\Models\Account;
use App\Models\FiscalYear;
use App\Models\Hotel;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    FiscalYear::create([
        'name' => now()->year,
        'starts_at' => now()->startOfYear(),
        'ends_at' => now()->endOfYear(),
        'status' => FiscalYearStatus::OPEN,
    ]);

    Account::create([
        'code' => '2211',
        'name' => 'الموردون',
        'class' => AccountClass::LIABILITIES,
        'type' => AccountType::HEADER,
        'normal_balance' => AccountNormalBalance::CREDIT,
        'level' => 1,
        'is_active' => true,
        'is_system' => false,
    ]);
});

it('creates a supplier with correct defaults', function () {
    $supplier = Supplier::factory()->create();

    expect($supplier)->toBeInstanceOf(Supplier::class)
        ->and($supplier->name)->not->toBeEmpty()
        ->and($supplier->type)->toBeInstanceOf(SupplierType::class)
        ->and($supplier->country)->toBe('SA');
});

it('auto-generates a code on creation', function () {
    $supplier = Supplier::factory()->create(['code' => null]);

    expect($supplier->code)->not->toBeNull()
        ->and($supplier->code)->toMatch('/^SUP-\d{4}-\d{5}$/');
});

it('generates sequential codes', function () {
    $s1 = Supplier::factory()->create(['code' => null]);
    $s2 = Supplier::factory()->create(['code' => null]);

    $seq1 = (int) substr($s1->code, -5);
    $seq2 = (int) substr($s2->code, -5);

    expect($seq2)->toBe($seq1 + 1);
});

it('auto-creates an accounting account on creation', function () {
    $supplier = Supplier::factory()->create();

    expect($supplier->account_id)->not->toBeNull();

    $account = Account::find($supplier->account_id);

    expect($account)->not->toBeNull()
        ->and($account->code)->toMatch('/^2211\d+$/')
        ->and($account->class)->toBe(AccountClass::LIABILITIES)
        ->and($account->normal_balance)->toBe(AccountNormalBalance::CREDIT)
        ->and($account->type)->toBe(AccountType::DETAIL)
        ->and($account->parent_id)->toBe(Account::where('code', '2211')->first()->id);
});

it('does not overwrite existing account_id', function () {
    $existingAccount = Account::create([
        'code' => '221199',
        'name' => 'حساب مورد مخصص',
        'class' => AccountClass::LIABILITIES,
        'type' => AccountType::DETAIL,
        'normal_balance' => AccountNormalBalance::CREDIT,
        'parent_id' => Account::where('code', '2211')->first()->id,
        'level' => 2,
        'is_active' => true,
        'is_system' => false,
    ]);

    $supplier = Supplier::factory()->create(['account_id' => $existingAccount->id]);

    expect($supplier->account_id)->toBe($existingAccount->id);
});

it('has an account relationship', function () {
    $supplier = Supplier::factory()->create();

    expect($supplier->account)->not->toBeNull()
        ->and($supplier->account->id)->toBe($supplier->account_id);
});

it('has a hotels relationship', function () {
    $supplier = Supplier::factory()->create();
    Hotel::factory()->for($supplier)->create(['name' => 'فندق التest']);

    expect($supplier->hotels)->toHaveCount(1)
        ->and($supplier->hotels->first()->name)->toBe('فندق التest');
});

it('uses code as route key name', function () {
    $supplier = Supplier::factory()->create();

    expect($supplier->getRouteKeyName())->toBe('code');
});

it('has the correct entity code type', function () {
    expect(Supplier::entityCodeType())->toBe('SUP');
});

it('casts type to SupplierType enum', function () {
    $supplier = Supplier::factory()->create(['type' => SupplierType::HOTEL]);

    expect($supplier->type)->toBe(SupplierType::HOTEL);
});

it('can be soft deleted and restored', function () {
    $supplier = Supplier::factory()->create();

    $supplier->delete();

    expect(Supplier::find($supplier->id))->toBeNull()
        ->and(Supplier::withTrashed()->find($supplier->id))->not->toBeNull();

    $supplier->restore();

    expect(Supplier::find($supplier->id))->not->toBeNull();
});

it('factory generates all supplier types', function () {
    $types = collect();
    for ($i = 0; $i < 20; $i++) {
        $supplier = Supplier::factory()->create(['code' => null]);
        $types->push($supplier->type);
    }

    expect($types->unique()->count())->toBeGreaterThan(1);
});

it('has many journal lines', function () {
    $supplier = Supplier::factory()->create();

    expect($supplier->journalLines)->toBeEmpty();
});
