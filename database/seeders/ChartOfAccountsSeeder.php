<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Hajj/Umrah Travel Agency Chart of Accounts Seeder
 * النظام ال لشركة عمرة وحج
 *
 * Structure: Class (1 digit) → Group (2 digits) → Sub-group (3 digits) → Detail (4+ digits)
 * All "header" accounts are NOT postable. Only "detail" accounts accept journal lines.
 */
class ChartOfAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = $this->getAccounts();

        $idMap = [];

        foreach (DB::table('accounts')->pluck('id', 'code') as $code => $id) {
            $idMap[$code] = $id;
        }

        foreach ($accounts as $account) {
            if (isset($idMap[$account['code']])) {
                continue;
            }

            $parentId = null;
            if ($account['parent_code'] !== null) {
                $parentId = $idMap[$account['parent_code']] ?? null;
            }

            $id = DB::table('accounts')->insertGetId([
                'code' => $account['code'],
                'name' => $account['name'],
                'name_en' => $account['name_en'] ?? null,
                'class' => $account['class'],
                'type' => $account['type'],
                'normal_balance' => $account['normal_balance'],
                'parent_id' => $parentId,
                'level' => $account['level'],
                'is_active' => true,
                'is_system' => $account['is_system'] ?? false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $idMap[$account['code']] = $id;
        }
    }

    private function getAccounts(): array
    {
        return [
            // ============================================================
            // CLASS 1 — ASSETS — أصول
            // ============================================================
            ['code' => '1', 'name' => 'الأصول', 'name_en' => 'Assets', 'class' => 'assets', 'type' => 'header', 'normal_balance' => 'debit', 'parent_code' => null, 'level' => 1, 'is_system' => true],

            // Non-current assets
            ['code' => '11', 'name' => 'الأصول غير المتداولة', 'name_en' => 'Non-Current Assets', 'class' => 'assets', 'type' => 'header', 'normal_balance' => 'debit', 'parent_code' => '1', 'level' => 2],
            ['code' => '111', 'name' => 'الأصول الثابتة', 'name_en' => 'Fixed Assets', 'class' => 'assets', 'type' => 'header', 'normal_balance' => 'debit', 'parent_code' => '11', 'level' => 3],
            ['code' => '1111', 'name' => 'الأراضي', 'name_en' => 'Land', 'class' => 'assets', 'type' => 'detail', 'normal_balance' => 'debit', 'parent_code' => '111', 'level' => 4],
            ['code' => '1112', 'name' => 'المباني', 'name_en' => 'Buildings', 'class' => 'assets', 'type' => 'detail', 'normal_balance' => 'debit', 'parent_code' => '111', 'level' => 4],
            ['code' => '1113', 'name' => 'مجمع إهلاك المباني', 'name_en' => 'Acc. Depreciation - Buildings', 'class' => 'assets', 'type' => 'detail', 'normal_balance' => 'credit', 'parent_code' => '111', 'level' => 4],
            ['code' => '1114', 'name' => 'السيارات والمركبات', 'name_en' => 'Vehicles', 'class' => 'assets', 'type' => 'detail', 'normal_balance' => 'debit', 'parent_code' => '111', 'level' => 4],
            ['code' => '1115', 'name' => 'مجمع إهلاك السيارات', 'name_en' => 'Acc. Depreciation - Vehicles', 'class' => 'assets', 'type' => 'detail', 'normal_balance' => 'credit', 'parent_code' => '111', 'level' => 4],
            ['code' => '1116', 'name' => 'الأثاث والتجهيزات', 'name_en' => 'Furniture & Fixtures', 'class' => 'assets', 'type' => 'detail', 'normal_balance' => 'debit', 'parent_code' => '111', 'level' => 4],
            ['code' => '1117', 'name' => 'مجمع إهلاك الأثاث', 'name_en' => 'Acc. Depreciation - Furniture', 'class' => 'assets', 'type' => 'detail', 'normal_balance' => 'credit', 'parent_code' => '111', 'level' => 4],
            ['code' => '1118', 'name' => 'الحاسبات والمعدات المكتبية', 'name_en' => 'Computers & Office Equipment', 'class' => 'assets', 'type' => 'detail', 'normal_balance' => 'debit', 'parent_code' => '111', 'level' => 4],
            ['code' => '1119', 'name' => 'مجمع إهلاك الحاسبات', 'name_en' => 'Acc. Depreciation - Computers', 'class' => 'assets', 'type' => 'detail', 'normal_balance' => 'credit', 'parent_code' => '111', 'level' => 4],

            // Current assets
            ['code' => '12', 'name' => 'الأصول المتداولة', 'name_en' => 'Current Assets', 'class' => 'assets', 'type' => 'header', 'normal_balance' => 'debit', 'parent_code' => '1', 'level' => 2],
            ['code' => '121', 'name' => 'المخزون', 'name_en' => 'Inventory', 'class' => 'assets', 'type' => 'header', 'normal_balance' => 'debit', 'parent_code' => '12', 'level' => 3],
            ['code' => '1211', 'name' => 'مخزون مستلزمات سفر', 'name_en' => 'Travel Supplies Inventory', 'class' => 'assets', 'type' => 'detail', 'normal_balance' => 'debit', 'parent_code' => '121', 'level' => 4],
            ['code' => '1212', 'name' => 'تأشيرات وحجوزات تحت التنفيذ', 'name_en' => 'Work In Progress - Bookings', 'class' => 'assets', 'type' => 'detail', 'normal_balance' => 'debit', 'parent_code' => '121', 'level' => 4],
            ['code' => '122', 'name' => 'الذمم المدينة', 'name_en' => 'Accounts Receivable', 'class' => 'assets', 'type' => 'header', 'normal_balance' => 'debit', 'parent_code' => '12', 'level' => 3],
            ['code' => '1221', 'name' => 'ذمم العملاء', 'name_en' => 'Trade Receivables', 'class' => 'assets', 'type' => 'detail', 'normal_balance' => 'debit', 'parent_code' => '122', 'level' => 4, 'is_system' => true],
            ['code' => '1223', 'name' => 'دفعات مقدمة للموردين', 'name_en' => 'Advances to Suppliers', 'class' => 'assets', 'type' => 'detail', 'normal_balance' => 'debit', 'parent_code' => '122', 'level' => 4],
            ['code' => '1224', 'name' => 'عهدة موظفين', 'name_en' => 'Employee Custodies', 'class' => 'assets', 'type' => 'detail', 'normal_balance' => 'debit', 'parent_code' => '122', 'level' => 4, 'is_system' => true],
            ['code' => '1225', 'name' => 'سلف الموظفين', 'name_en' => 'Employee Advances', 'class' => 'assets', 'type' => 'detail', 'normal_balance' => 'debit', 'parent_code' => '122', 'level' => 4, 'is_system' => true],
            ['code' => '1226', 'name' => 'ضريبة المدخلات (مدخلات ض.ق.م)', 'name_en' => 'Input VAT', 'class' => 'assets', 'type' => 'detail', 'normal_balance' => 'debit', 'parent_code' => '122', 'level' => 4],
            ['code' => '123', 'name' => 'النقدية وما يعادلها', 'name_en' => 'Cash & Equivalents', 'class' => 'assets', 'type' => 'header', 'normal_balance' => 'debit', 'parent_code' => '12', 'level' => 3],
            ['code' => '1231', 'name' => 'الخزينة الرئيسية', 'name_en' => 'Main Safe', 'class' => 'assets', 'type' => 'detail', 'normal_balance' => 'debit', 'parent_code' => '123', 'level' => 4, 'is_system' => true],
            ['code' => '1232', 'name' => 'خزينة الفرع', 'name_en' => 'Branch Safe', 'class' => 'assets', 'type' => 'detail', 'normal_balance' => 'debit', 'parent_code' => '123', 'level' => 4],
            ['code' => '1233', 'name' => 'حسابات بنكية', 'name_en' => 'Bank Accounts', 'class' => 'assets', 'type' => 'detail', 'normal_balance' => 'debit', 'parent_code' => '123', 'level' => 4, 'is_system' => true],

            // ============================================================
            // CLASS 2 — LIABILITIES — التزامات
            // ============================================================
            ['code' => '2', 'name' => 'الالتزامات', 'name_en' => 'Liabilities', 'class' => 'liabilities', 'type' => 'header', 'normal_balance' => 'credit', 'parent_code' => null, 'level' => 1, 'is_system' => true],
            ['code' => '21', 'name' => 'الالتزامات غير المتداولة', 'name_en' => 'Non-Current Liabilities', 'class' => 'liabilities', 'type' => 'header', 'normal_balance' => 'credit', 'parent_code' => '2', 'level' => 2],
            ['code' => '2111', 'name' => 'قروض طويلة الأجل', 'name_en' => 'Long-Term Loans', 'class' => 'liabilities', 'type' => 'detail', 'normal_balance' => 'credit', 'parent_code' => '21', 'level' => 3],
            ['code' => '22', 'name' => 'الالتزامات المتداولة', 'name_en' => 'Current Liabilities', 'class' => 'liabilities', 'type' => 'header', 'normal_balance' => 'credit', 'parent_code' => '2', 'level' => 2],
            ['code' => '221', 'name' => 'الذمم الدائنة', 'name_en' => 'Accounts Payable', 'class' => 'liabilities', 'type' => 'header', 'normal_balance' => 'credit', 'parent_code' => '22', 'level' => 3],
            ['code' => '2211', 'name' => 'ذمم الموردين', 'name_en' => 'Trade Payables', 'class' => 'liabilities', 'type' => 'detail', 'normal_balance' => 'credit', 'parent_code' => '221', 'level' => 4, 'is_system' => true],
            ['code' => '2212', 'name' => 'ذمم الفنادق', 'name_en' => 'Hotels Payable', 'class' => 'liabilities', 'type' => 'detail', 'normal_balance' => 'credit', 'parent_code' => '221', 'level' => 4, 'is_system' => true],
            ['code' => '2213', 'name' => 'دفعات مقدمة من العملاء', 'name_en' => 'Customer Advance Payments', 'class' => 'liabilities', 'type' => 'detail', 'normal_balance' => 'credit', 'parent_code' => '221', 'level' => 4],
            ['code' => '222', 'name' => 'الالتزامات الضريبية', 'name_en' => 'Tax Liabilities', 'class' => 'liabilities', 'type' => 'header', 'normal_balance' => 'credit', 'parent_code' => '22', 'level' => 3],
            ['code' => '2221', 'name' => 'ضريبة القيمة المضافة المستحقة', 'name_en' => 'VAT Payable', 'class' => 'liabilities', 'type' => 'detail', 'normal_balance' => 'credit', 'parent_code' => '222', 'level' => 4, 'is_system' => true],
            ['code' => '2222', 'name' => 'خصم وإضافة مستقطع', 'name_en' => 'Withholding Tax Payable', 'class' => 'liabilities', 'type' => 'detail', 'normal_balance' => 'credit', 'parent_code' => '222', 'level' => 4, 'is_system' => true],
            ['code' => '2223', 'name' => 'ضريبة دمغة مستحقة', 'name_en' => 'Stamp Tax Payable', 'class' => 'liabilities', 'type' => 'detail', 'normal_balance' => 'credit', 'parent_code' => '222', 'level' => 4],
            ['code' => '2224', 'name' => 'ضريبة الدخل مستحقة', 'name_en' => 'Income Tax Payable', 'class' => 'liabilities', 'type' => 'detail', 'normal_balance' => 'credit', 'parent_code' => '222', 'level' => 4],
            ['code' => '223', 'name' => 'مصروفات مستحقة', 'name_en' => 'Accrued Expenses', 'class' => 'liabilities', 'type' => 'header', 'normal_balance' => 'credit', 'parent_code' => '22', 'level' => 3],
            ['code' => '2231', 'name' => 'رواتب مستحقة', 'name_en' => 'Accrued Salaries', 'class' => 'liabilities', 'type' => 'detail', 'normal_balance' => 'credit', 'parent_code' => '223', 'level' => 4, 'is_system' => true],
            ['code' => '2232', 'name' => 'تأمينات اجتماعية مستحقة', 'name_en' => 'Social Insurance Payable', 'class' => 'liabilities', 'type' => 'detail', 'normal_balance' => 'credit', 'parent_code' => '223', 'level' => 4],

            // ============================================================
            // CLASS 3 — EQUITY — حقوق الملكية
            // ============================================================
            ['code' => '3', 'name' => 'حقوق الملكية', 'name_en' => 'Equity', 'class' => 'equity', 'type' => 'header', 'normal_balance' => 'credit', 'parent_code' => null, 'level' => 1, 'is_system' => true],
            ['code' => '31', 'name' => 'رأس المال', 'name_en' => 'Share Capital', 'class' => 'equity', 'type' => 'header', 'normal_balance' => 'credit', 'parent_code' => '3', 'level' => 2],
            ['code' => '3111', 'name' => 'جاري الشركاء', 'name_en' => 'Partners Current Account', 'class' => 'equity', 'type' => 'detail', 'normal_balance' => 'credit', 'parent_code' => '31', 'level' => 3, 'is_system' => true],
            ['code' => '32', 'name' => 'الاحتياطيات', 'name_en' => 'Reserves', 'class' => 'equity', 'type' => 'header', 'normal_balance' => 'credit', 'parent_code' => '3', 'level' => 2],
            ['code' => '3211', 'name' => 'احتياطي قانوني', 'name_en' => 'Legal Reserve', 'class' => 'equity', 'type' => 'detail', 'normal_balance' => 'credit', 'parent_code' => '32', 'level' => 3],
            ['code' => '3212', 'name' => 'احتياطي عام', 'name_en' => 'General Reserve', 'class' => 'equity', 'type' => 'detail', 'normal_balance' => 'credit', 'parent_code' => '32', 'level' => 3],
            ['code' => '33', 'name' => 'الأرباح المبقاة', 'name_en' => 'Retained Earnings', 'class' => 'equity', 'type' => 'header', 'normal_balance' => 'credit', 'parent_code' => '3', 'level' => 2],
            ['code' => '3311', 'name' => 'أرباح مبقاة من سنوات سابقة', 'name_en' => 'Prior Years Retained Earnings', 'class' => 'equity', 'type' => 'detail', 'normal_balance' => 'credit', 'parent_code' => '33', 'level' => 3, 'is_system' => true],
            ['code' => '3312', 'name' => 'صافي ربح/خسارة السنة الحالية', 'name_en' => 'Current Year Net Income', 'class' => 'equity', 'type' => 'detail', 'normal_balance' => 'credit', 'parent_code' => '33', 'level' => 3, 'is_system' => true],

            // ============================================================
            // CLASS 4 — REVENUE — الإيرادات
            // ============================================================
            ['code' => '4', 'name' => 'الإيرادات', 'name_en' => 'Revenue', 'class' => 'revenue', 'type' => 'header', 'normal_balance' => 'credit', 'parent_code' => null, 'level' => 1, 'is_system' => true],
            ['code' => '41', 'name' => 'إيرادات الخدمات', 'name_en' => 'Service Revenue', 'class' => 'revenue', 'type' => 'header', 'normal_balance' => 'credit', 'parent_code' => '4', 'level' => 2],
            ['code' => '4111', 'name' => 'إيرادات باقات العمرة', 'name_en' => 'Umrah Package Revenue', 'class' => 'revenue', 'type' => 'detail', 'normal_balance' => 'credit', 'parent_code' => '41', 'level' => 3, 'is_system' => true],
            ['code' => '4112', 'name' => 'إيرادات باقات الحج', 'name_en' => 'Hajj Package Revenue', 'class' => 'revenue', 'type' => 'detail', 'normal_balance' => 'credit', 'parent_code' => '41', 'level' => 3],
            ['code' => '4113', 'name' => 'إيرادات تذاكر الطيران', 'name_en' => 'Airline Ticket Revenue', 'class' => 'revenue', 'type' => 'detail', 'normal_balance' => 'credit', 'parent_code' => '41', 'level' => 3],
            ['code' => '4114', 'name' => 'إيرادات التأشيرات', 'name_en' => 'Visa Revenue', 'class' => 'revenue', 'type' => 'detail', 'normal_balance' => 'credit', 'parent_code' => '41', 'level' => 3],
            ['code' => '4115', 'name' => 'إيرادات خدمات إضافية', 'name_en' => 'Add-on Services Revenue', 'class' => 'revenue', 'type' => 'detail', 'normal_balance' => 'credit', 'parent_code' => '41', 'level' => 3],
            ['code' => '42', 'name' => 'إيرادات أخرى', 'name_en' => 'Other Revenue', 'class' => 'revenue', 'type' => 'header', 'normal_balance' => 'credit', 'parent_code' => '4', 'level' => 2],
            ['code' => '4211', 'name' => 'إيرادات عمولات', 'name_en' => 'Commission Revenue', 'class' => 'revenue', 'type' => 'detail', 'normal_balance' => 'credit', 'parent_code' => '42', 'level' => 3],
            ['code' => '4212', 'name' => 'إيرادات متنوعة', 'name_en' => 'Miscellaneous Revenue', 'class' => 'revenue', 'type' => 'detail', 'normal_balance' => 'credit', 'parent_code' => '42', 'level' => 3],

            // ============================================================
            // CLASS 5 — EXPENSES — المصروفات
            // ============================================================
            ['code' => '5', 'name' => 'المصروفات', 'name_en' => 'Expenses', 'class' => 'expenses', 'type' => 'header', 'normal_balance' => 'debit', 'parent_code' => null, 'level' => 1, 'is_system' => true],

            // Direct service costs
            ['code' => '51', 'name' => 'تكاليف الخدمات المباشرة', 'name_en' => 'Direct Service Costs', 'class' => 'expenses', 'type' => 'header', 'normal_balance' => 'debit', 'parent_code' => '5', 'level' => 2],
            ['code' => '511', 'name' => 'تكاليف الإقامة', 'name_en' => 'Accommodation Costs', 'class' => 'expenses', 'type' => 'header', 'normal_balance' => 'debit', 'parent_code' => '51', 'level' => 3],
            ['code' => '5111', 'name' => 'تكلفة الفنادق - مكة', 'name_en' => 'Hotel Costs - Makkah', 'class' => 'expenses', 'type' => 'detail', 'normal_balance' => 'debit', 'parent_code' => '511', 'level' => 4, 'is_system' => true],
            ['code' => '5112', 'name' => 'تكلفة الفنادق - المدينة', 'name_en' => 'Hotel Costs - Madinah', 'class' => 'expenses', 'type' => 'detail', 'normal_balance' => 'debit', 'parent_code' => '511', 'level' => 4],
            ['code' => '5113', 'name' => 'تكلفة الفنادق - أخرى', 'name_en' => 'Hotel Costs - Other', 'class' => 'expenses', 'type' => 'detail', 'normal_balance' => 'debit', 'parent_code' => '511', 'level' => 4],
            ['code' => '512', 'name' => 'تكاليف السفر', 'name_en' => 'Travel Costs', 'class' => 'expenses', 'type' => 'header', 'normal_balance' => 'debit', 'parent_code' => '51', 'level' => 3],
            ['code' => '5121', 'name' => 'تكلفة تذاكر الطيران', 'name_en' => 'Airline Ticket Costs', 'class' => 'expenses', 'type' => 'detail', 'normal_balance' => 'debit', 'parent_code' => '512', 'level' => 4, 'is_system' => true],
            ['code' => '5122', 'name' => 'تكلفة التأشيرات', 'name_en' => 'Visa Costs', 'class' => 'expenses', 'type' => 'detail', 'normal_balance' => 'debit', 'parent_code' => '512', 'level' => 4],
            ['code' => '5123', 'name' => 'تكلفة الانتقالات والمواصلات', 'name_en' => 'Transportation Costs', 'class' => 'expenses', 'type' => 'detail', 'normal_balance' => 'debit', 'parent_code' => '512', 'level' => 4],
            ['code' => '513', 'name' => 'تكاليف التشغيل', 'name_en' => 'Operational Costs', 'class' => 'expenses', 'type' => 'header', 'normal_balance' => 'debit', 'parent_code' => '51', 'level' => 3],
            ['code' => '5131', 'name' => 'تكلفة الإعاشة والوجبات', 'name_en' => 'Catering Costs', 'class' => 'expenses', 'type' => 'detail', 'normal_balance' => 'debit', 'parent_code' => '513', 'level' => 4],
            ['code' => '5132', 'name' => 'تكلفة المرشدين', 'name_en' => 'Tour Guide Costs', 'class' => 'expenses', 'type' => 'detail', 'normal_balance' => 'debit', 'parent_code' => '513', 'level' => 4],
            ['code' => '5133', 'name' => 'تكلفة العمالة المؤقتة', 'name_en' => 'Temporary Labor Costs', 'class' => 'expenses', 'type' => 'detail', 'normal_balance' => 'debit', 'parent_code' => '513', 'level' => 4],
            ['code' => '5134', 'name' => 'مصاريف رحلات وجولات', 'name_en' => 'Excursion Costs', 'class' => 'expenses', 'type' => 'detail', 'normal_balance' => 'debit', 'parent_code' => '513', 'level' => 4],

            // General & admin expenses
            ['code' => '52', 'name' => 'المصروفات العمومية والإدارية', 'name_en' => 'General & Admin Expenses', 'class' => 'expenses', 'type' => 'header', 'normal_balance' => 'debit', 'parent_code' => '5', 'level' => 2],
            ['code' => '5211', 'name' => 'رواتب الإداريين', 'name_en' => 'Admin Staff Salaries', 'class' => 'expenses', 'type' => 'detail', 'normal_balance' => 'debit', 'parent_code' => '52', 'level' => 3, 'is_system' => true],
            ['code' => '5212', 'name' => 'إيجار المكتب', 'name_en' => 'Office Rent', 'class' => 'expenses', 'type' => 'detail', 'normal_balance' => 'debit', 'parent_code' => '52', 'level' => 3],
            ['code' => '5213', 'name' => 'مرافق (كهرباء - مياه - غاز)', 'name_en' => 'Utilities', 'class' => 'expenses', 'type' => 'detail', 'normal_balance' => 'debit', 'parent_code' => '52', 'level' => 3],
            ['code' => '5214', 'name' => 'اتصالات وإنترنت', 'name_en' => 'Telecom & Internet', 'class' => 'expenses', 'type' => 'detail', 'normal_balance' => 'debit', 'parent_code' => '52', 'level' => 3],
            ['code' => '5215', 'name' => 'مصروفات سيارات', 'name_en' => 'Vehicle Expenses', 'class' => 'expenses', 'type' => 'detail', 'normal_balance' => 'debit', 'parent_code' => '52', 'level' => 3],
            ['code' => '5216', 'name' => 'قرطاسية ومستلزمات مكتبية', 'name_en' => 'Office Supplies & Stationery', 'class' => 'expenses', 'type' => 'detail', 'normal_balance' => 'debit', 'parent_code' => '52', 'level' => 3],
            ['code' => '5217', 'name' => 'رسوم قانونية ومهنية', 'name_en' => 'Legal & Professional Fees', 'class' => 'expenses', 'type' => 'detail', 'normal_balance' => 'debit', 'parent_code' => '52', 'level' => 3],
            ['code' => '5218', 'name' => 'تأمينات', 'name_en' => 'Insurance', 'class' => 'expenses', 'type' => 'detail', 'normal_balance' => 'debit', 'parent_code' => '52', 'level' => 3],
            ['code' => '5219', 'name' => 'رسوم وتراخيص', 'name_en' => 'Licenses & Permits', 'class' => 'expenses', 'type' => 'detail', 'normal_balance' => 'debit', 'parent_code' => '52', 'level' => 3],
            ['code' => '5220', 'name' => 'مصروفات تدريب وتطوير', 'name_en' => 'Training & Development', 'class' => 'expenses', 'type' => 'detail', 'normal_balance' => 'debit', 'parent_code' => '52', 'level' => 3],
            ['code' => '5221', 'name' => 'مصروفات تسويق وإعلان', 'name_en' => 'Marketing & Advertising', 'class' => 'expenses', 'type' => 'detail', 'normal_balance' => 'debit', 'parent_code' => '52', 'level' => 3],

            // Finance costs
            ['code' => '53', 'name' => 'التكاليف التمويلية', 'name_en' => 'Finance Costs', 'class' => 'expenses', 'type' => 'header', 'normal_balance' => 'debit', 'parent_code' => '5', 'level' => 2],
            ['code' => '5311', 'name' => 'فوائد بنكية', 'name_en' => 'Bank Interest', 'class' => 'expenses', 'type' => 'detail', 'normal_balance' => 'debit', 'parent_code' => '53', 'level' => 3],
            ['code' => '5312', 'name' => 'عمولات بنكية', 'name_en' => 'Bank Charges & Commissions', 'class' => 'expenses', 'type' => 'detail', 'normal_balance' => 'debit', 'parent_code' => '53', 'level' => 3],

            // Tax expenses
            ['code' => '54', 'name' => 'مصروفات الضرائب', 'name_en' => 'Tax Expenses', 'class' => 'expenses', 'type' => 'header', 'normal_balance' => 'debit', 'parent_code' => '5', 'level' => 2],
            ['code' => '5411', 'name' => 'ضريبة الدخل', 'name_en' => 'Income Tax Expense', 'class' => 'expenses', 'type' => 'detail', 'normal_balance' => 'debit', 'parent_code' => '54', 'level' => 3],
            ['code' => '5412', 'name' => 'ضريبة دمغة', 'name_en' => 'Stamp Tax Expense', 'class' => 'expenses', 'type' => 'detail', 'normal_balance' => 'debit', 'parent_code' => '54', 'level' => 3],
        ];
    }
}
