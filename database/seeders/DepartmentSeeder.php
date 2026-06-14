<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            'الإدارة التنفيذية',
            'المحاسبة والمالية',
            'المبيعات والتسويق',
            'العمليات الميدانية',
            'الحجوزات وإصدار التذاكر',
            'خدمة العملاء والدعم',
            'الموارد البشرية والشؤون الإدارية',
            'النقل والشحن',
        ];

        foreach ($departments as $name) {
            Department::firstOrCreate(['name' => $name, 'is_active' => true]);
        }
    }
}
