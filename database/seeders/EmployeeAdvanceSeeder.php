<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\EmployeeAdvance;
use App\Models\Safe;
use Illuminate\Database\Seeder;

class EmployeeAdvanceSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::where('is_active', true)->get();
        $safe = Safe::first();

        if ($employees->isEmpty() || ! $safe) {
            return;
        }

        foreach ($employees->take(3) as $employee) {
            EmployeeAdvance::firstOrCreate(
                ['employee_id' => $employee->id, 'advance_date' => now()->subMonths(2)],
                [
                    'amount' => 2000,
                    'installments' => 3,
                    'type' => 'short_term',
                    'status' => 'active',
                    'safe_id' => $safe->id,
                ]
            );
        }

        foreach ($employees->take(2) as $employee) {
            EmployeeAdvance::firstOrCreate(
                ['employee_id' => $employee->id, 'advance_date' => now()->subMonths(4)],
                [
                    'amount' => 5000,
                    'installments' => 6,
                    'type' => 'long_term',
                    'status' => 'active',
                    'safe_id' => $safe->id,
                ]
            );
        }
    }
}
