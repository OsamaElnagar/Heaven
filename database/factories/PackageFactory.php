<?php

namespace Database\Factories;

use App\Enums\PackageGrade;
use App\Enums\PackageType;
use App\Models\Package;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Package> */
class PackageFactory extends Factory
{
    protected $model = Package::class;

    private static array $hajjNames = [
        'باقة الحج الاقتصادي', 'باقة الحج المميز', 'باقة حج VIP',
        'باقة الحج العائلي', 'باقة الحج الذهبية', 'باقة الحج الماسية',
    ];

    private static array $umrahNames = [
        'باقة عمرة رمضان', 'باقة عمرة شوال', 'باقة عمرة رجب',
        'باقة عمرة مميزة', 'باقة العمرة الاقتصادية', 'باقة عمرة VIP',
    ];

    public function definition(): array
    {
        $type = fake()->randomElement(PackageType::cases());
        $grade = fake()->randomElement(PackageGrade::cases());
        $seasonYear = fake()->numberBetween((int) now()->year - 1, (int) now()->year + 1);
        $nights = match ($type) {
            PackageType::HAJJ => fake()->randomElement([14, 15, 21, 28, 30]),
            PackageType::UMRAH => fake()->randomElement([7, 10, 14, 15, 21]),
        };
        $basePrice = match ($grade) {
            PackageGrade::ECONOMY => fake()->numberBetween(40000, 70000),
            PackageGrade::STANDARD => fake()->numberBetween(70000, 120000),
            PackageGrade::VIP => fake()->numberBetween(120000, 200000),
            PackageGrade::VVIP => fake()->numberBetween(200000, 400000),
        };

        $departure = Carbon::create($seasonYear, fake()->numberBetween(1, 12), fake()->numberBetween(1, 28));

        return [
            'name' => $type === PackageType::HAJJ
                ? fake()->randomElement(self::$hajjNames)
                : fake()->randomElement(self::$umrahNames),
            'type' => $type,
            'grade' => $grade,
            'season_year' => $seasonYear,
            'duration_nights' => $nights,
            'base_price' => $basePrice,
            'total_seats' => fake()->numberBetween(30, 200),
            'reserved_seats' => 0,
            'departure_date' => $departure,
            'return_date' => (clone $departure)->addDays($nights),
            'includes' => '- الإقامة في فنادق ٤ نجوم
- وجبات مفتوحة (بوفيه)
- انتقالات المطار
- تأشيرة
- مشرف رحلة',
            'excludes' => fake()->boolean(50) ? '- تذكرة الطيران
- المصروفات الشخصية' : null,
            'notes' => fake()->boolean(30) ? fake()->sentence(5) : null,
            'is_active' => $seasonYear >= (int) now()->year,
        ];
    }
}
