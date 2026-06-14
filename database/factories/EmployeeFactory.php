<?php

namespace Database\Factories;

use App\Enums\EmployeeType;
use App\Enums\SalaryType;
use App\Models\Department;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Employee> */
class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    private static array $names = [
        'محمد', 'أحمد', 'محمود', 'خالد', 'عمرو', 'مصطفى', 'هاني', 'وليد', 'كريم', 'أسامة',
    ];

    private static array $lastNames = ['السيد', 'إبراهيم', 'حسن', 'علي', 'محمد', 'محمود', 'جاد', 'سعيد'];

    private static array $roles = [
        'sales' => 'مندوب مبيعات',
        'operations' => 'مسؤول عمليات',
        'accountant' => 'محاسب',
        'guide' => 'مرشد ديني',
        'manager' => 'مدير',
    ];

    public function definition(): array
    {
        $role = fake()->randomElement(array_keys(self::$roles));
        $salaryType = $role === 'guide' ? SalaryType::PER_TRIP : fake()->randomElement(SalaryType::cases());
        $baseSalary = match ($salaryType) {
            SalaryType::MONTHLY => fake()->numberBetween(5000, 20000),
            SalaryType::DAILY => fake()->numberBetween(200, 800),
            SalaryType::HOURLY => fake()->numberBetween(50, 150),
            SalaryType::PER_TRIP => fake()->numberBetween(3000, 10000),
            SalaryType::COMMISSION => fake()->numberBetween(2000, 5000),
        };

        return [
            'name' => self::$names[array_rand(self::$names)].' '.self::$lastNames[array_rand(self::$lastNames)],
            'national_id' => '2'.fake()->numerify('#############'),
            'phone' => '01'.fake()->randomElement([0, 1, 2, 5]).fake()->numerify('#########'),
            'role' => $role,
            'job_title' => self::$roles[$role] ?? null,
            'type' => fake()->randomElement(EmployeeType::cases()),
            'salary_type' => $salaryType,
            'base_salary' => $baseSalary,
            'daily_hours' => 8,
            'hire_date' => Carbon::now()->subMonths(fake()->numberBetween(1, 36)),
            'termination_date' => null,
            'is_active' => true,
            'department_id' => Department::factory(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => [
            'is_active' => false,
            'termination_date' => Carbon::now()->subDays(fake()->numberBetween(1, 90)),
        ]);
    }
}
