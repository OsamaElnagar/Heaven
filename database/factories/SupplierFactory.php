<?php

namespace Database\Factories;

use App\Enums\SupplierType;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Supplier> */
class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    private static array $hotelSuppliers = [
        ['دار التوحيد', 'مكة المكرمة'],
        ['الفندق plaza', 'مكة المكرمة'],
        ['مجموعة فنادق الحرم', 'المدينة المنورة'],
        ['الضيافة العربية', 'جدة'],
    ];

    private static array $airlineSuppliers = [
        ['مصر للطيران', 'القاهرة'],
        ['العربية للطيران', 'الشارقة'],
        ['نسما للطيران', 'القاهرة'],
    ];

    private static array $transportSuppliers = [
        ['النقل الجماعي', 'جدة'],
        ['سابتكو', 'الرياض'],
        ['حافلات الجزيرة', 'مكة المكرمة'],
    ];

    private static array $cateringSuppliers = [
        ['الولائم الذهبية', 'مكة المكرمة'],
        ['مطابخ الضيافة', 'المدينة المنورة'],
    ];

    public function definition(): array
    {
        $type = fake()->randomElement(SupplierType::cases());

        $entry = match ($type) {
            SupplierType::HOTEL => fake()->randomElement(self::$hotelSuppliers),
            SupplierType::AIRLINE => fake()->randomElement(self::$airlineSuppliers),
            SupplierType::TRANSPORT => fake()->randomElement(self::$transportSuppliers),
            SupplierType::CATERING => fake()->randomElement(self::$cateringSuppliers),
            SupplierType::OTHER => ['مؤسسة '.fake()->company(), fake()->randomElement(['الرياض', 'جدة', 'القاهرة', 'مكة المكرمة'])],
        };

        return [
            'name' => $entry[0],
            'type' => $type,
            'country' => 'SA',
            'city' => $entry[1],
            'contact_person' => fake()->boolean(70) ? fake()->name('male') : null,
            'phone' => fake()->boolean(80) ? '05'.fake()->numberBetween(10000000, 99999999) : null,
            'email' => fake()->boolean(50) ? fake()->companyEmail() : null,
            'notes' => fake()->boolean(20) ? fake()->sentence(4) : null,
        ];
    }
}
