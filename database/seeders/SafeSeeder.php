<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\Safe;
use Illuminate\Database\Seeder;

class SafeSeeder extends Seeder
{
    public function run(): void
    {
        Safe::firstOrCreate(
            ['code' => 'SAF-2026-00001'],
            [
                'name' => 'الخزينة الرئيسية',
                'is_active' => true,
                'notes' => 'الخزينة الرئيسية للشركة',
            ]
        );

        BankAccount::firstOrCreate(
            ['code' => 'BNK-2026-00001'],
            [
                'bank_name' => 'البنك الأهلي المصري',
                'branch' => 'فرع التحرير',
                'account_number' => '1234567890',
                'iban' => 'EG38001900050000000001234567',
                'is_active' => true,
                'notes' => 'حساب الشركة الرئيسي',
            ]
        );
    }
}
