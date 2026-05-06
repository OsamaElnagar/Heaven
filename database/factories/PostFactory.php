<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Post> */
class PostFactory extends Factory
{
    protected $model = Post::class;

    private static array $titles = [
        'فتح باب التسجيل لموسم الحج',
        'تحديثات مهمة حول تأشيرات العمرة',
        'باقات جديدة لموسم رمضان',
        'نصائح للحجاج قبل السفر',
        'إعلان مواعيد رحلات العمرة',
        'شراكتنا الجديدة مع فنادق الخمس نجوم',
        'تخفيضات خاصة للعائلات',
    ];

    public function definition(): array
    {
        $title = fake()->randomElement(self::$titles);

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::random(6),
            'excerpt' => fake()->sentence(15),
            'content' => implode("\n\n", fake()->paragraphs(4)),
            'is_published' => true,
            'published_at' => fake()->dateTimeBetween('-2 months', 'now'),
        ];
    }
}
