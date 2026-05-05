<?php

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Expense> */
class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    private static array $descriptions = [
        'office' => ['إيجار مكتب', 'فواتير كهرباء ومياه', 'مستلزمات مكتبية', 'صيانة أجهزة', 'اشتراك إنترنت'],
        'marketing' => ['إعلان فيسبوك', 'إعلان جوجل', 'طباعة بروشورات', 'تصميم إعلانات', 'حملة تسويقية'],
        'transport' => ['تأجير حافلة', 'وقود', 'صيانة مركبات', 'رسوم مواقف', 'تراخيص نقل'],
        'hotel_cost' => ['حجز فندق', 'دفعة مقدمة فندق', 'تكاليف إضافية فندق'],
        'airline_cost' => ['تذاكر طيران', 'رسوم أمتعة', 'تغيير حجوزات طيران'],
        'other' => ['مصاريف متنوعة', 'رسوم حكومية', 'تأمين', 'إكراميات', 'مستلزمات طبية'],
    ];

    public function definition(): array
    {
        $category = fake()->randomElement(array_keys(self::$descriptions));

        return [
            'category' => $category,
            'description' => fake()->randomElement(self::$descriptions[$category]),
            'amount' => fake()->numberBetween(500, 50000),
            'payment_method' => fake()->randomElement(PaymentMethod::cases()),
            'paid_at' => Carbon::now()->subDays(fake()->numberBetween(1, 120)),
            'receipt_path' => null,
            'notes' => fake()->boolean(20) ? fake()->sentence(3) : null,
        ];
    }
}
