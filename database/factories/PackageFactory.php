<?php

namespace Database\Factories;

use App\Enums\PackageGrade;
use App\Models\Package;
use App\Models\PackageType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Package> */
class PackageFactory extends Factory
{
    protected $model = Package::class;

    public function definition(): array
    {
        $type = PackageType::inRandomOrder()->first();
        $grade = fake()->randomElement(PackageGrade::cases());
        $seasonYear = fake()->numberBetween((int) now()->year - 1, (int) now()->year + 1);
        $nights = fake()->numberBetween(
            $type->duration_nights_min ?? 7,
            $type->duration_nights_max ?? 30
        );
        $basePrice = match ($grade) {
            PackageGrade::ECONOMY => fake()->numberBetween(40000, 70000),
            PackageGrade::STANDARD => fake()->numberBetween(70000, 120000),
            PackageGrade::VIP => fake()->numberBetween(120000, 200000),
            PackageGrade::VVIP => fake()->numberBetween(200000, 400000),
        };

        $departure = Carbon::create($seasonYear, fake()->numberBetween(1, 12), fake()->numberBetween(1, 28));

        return [
            'name' => 'باقة '.$type->name_ar.' - '.$grade->getLabel(),
            'type_id' => $type->id,
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
