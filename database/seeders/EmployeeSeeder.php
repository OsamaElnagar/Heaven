<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        if (Employee::count() > 0) {
            return;
        }

        $departmentIds = Department::pluck('id')->toArray();

        $employees = [
            ['name' => 'أحمد محمود حسن', 'phone' => '01234567890', 'national_id' => '29801011234567', 'role' => 'manager', 'job_title' => 'مدير عام', 'type' => 'permanent', 'base_salary' => 15000],
            ['name' => 'محمد علي إبراهيم', 'phone' => '01234567891', 'national_id' => '29503021234568', 'role' => 'accountant', 'job_title' => 'محاسب أول', 'type' => 'permanent', 'base_salary' => 10000],
            ['name' => 'خالد عبدالله السيد', 'phone' => '01234567892', 'national_id' => '29005151234569', 'role' => 'operations', 'job_title' => 'مسؤول عمليات', 'type' => 'permanent', 'base_salary' => 8000],
            ['name' => 'محمود السيد جاد', 'phone' => '01234567893', 'national_id' => '29207101234570', 'role' => 'sales', 'job_title' => 'مندوب مبيعات', 'type' => 'permanent', 'base_salary' => 6000],
            ['name' => 'هاني كريم محمد', 'phone' => '01234567894', 'national_id' => '29808151234571', 'role' => 'guide', 'job_title' => 'مرشد ديني', 'type' => 'permanent', 'base_salary' => 7000],
            ['name' => 'وليد سعيد حسن', 'phone' => '01234567895', 'national_id' => '29509121234572', 'role' => 'operations', 'job_title' => 'مشرف مواقع', 'type' => 'permanent', 'base_salary' => 9000],
            ['name' => 'كريم إبراهيم السيد', 'phone' => '01234567896', 'national_id' => '29711231234573', 'role' => 'accountant', 'job_title' => 'محاسب', 'type' => 'permanent', 'base_salary' => 5500],
            ['name' => 'أسامة محمود علي', 'phone' => '01234567897', 'national_id' => '29802151234574', 'role' => 'sales', 'job_title' => 'مندوب مبيعات', 'type' => 'temporary', 'base_salary' => 4000],
            ['name' => 'عمرو حسن محمد', 'phone' => '01234567898', 'national_id' => '29604181234575', 'role' => 'guide', 'job_title' => 'مرشد', 'type' => 'contracted', 'base_salary' => 5000],
            ['name' => 'مصطفى أحمد سعيد', 'phone' => '01234567899', 'national_id' => '29306171234576', 'role' => 'operations', 'job_title' => 'سائق', 'type' => 'daily', 'base_salary' => 300],
        ];

        foreach ($employees as $index => $data) {
            $deptId = $departmentIds[$index % count($departmentIds)];

            Employee::firstOrCreate(
                ['national_id' => $data['national_id']],
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'role' => $data['role'],
                    'job_title' => $data['job_title'],
                    'type' => $data['type'],
                    'salary_type' => $data['type'] === 'daily' ? 'daily' : 'monthly',
                    'base_salary' => $data['base_salary'],
                    'daily_hours' => 8,
                    'hire_date' => now()->subMonths(rand(6, 36)),
                    'is_active' => true,
                    'department_id' => $deptId,
                ]
            );
        }
    }
}
