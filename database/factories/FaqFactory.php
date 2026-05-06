<?php

namespace Database\Factories;

use App\Models\Faq;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Faq> */
class FaqFactory extends Factory
{
    protected $model = Faq::class;

    private static array $questions = [
        'ما هي المستندات المطلوبة للحصول على تأشيرة الحج؟',
        'هل يمكن للمرأة السفر بدون محرم؟',
        'ما هي طرق الدفع المتاحة؟',
        'ما هي سياسة الإلغاء والاسترداد؟',
        'هل تشمل الباقة تذاكر الطيران؟',
        'كم مدة إصدار التأشيرة؟',
        'هل يوجد مشرف مرافق للرحلة؟',
        'ما هي درجات الفنادق المتاحة؟',
    ];

    public function definition(): array
    {
        return [
            'question' => fake()->unique()->randomElement(self::$questions),
            'answer' => fake()->paragraphs(2, true),
            'sort_order' => fake()->numberBetween(0, 100),
            'is_published' => true,
        ];
    }
}
