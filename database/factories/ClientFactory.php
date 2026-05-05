<?php

namespace Database\Factories;

use App\Enums\Gender;
use App\Enums\MaritalStatus;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Client> */
class ClientFactory extends Factory
{
    protected $model = Client::class;

    private static array $maleNames = [
        'محمد', 'أحمد', 'محمود', 'علي', 'حسن', 'إبراهيم', 'خالد', 'عمرو', 'مصطفى', 'طارق',
        'شريف', 'هاني', 'وليد', 'سامح', 'كريم', 'أسامة', 'نادر', 'أشرف', 'جمال', 'رامي',
    ];

    private static array $femaleNames = [
        'فاطمة', 'مريم', 'آية', 'نور', 'أميرة', 'هدى', 'سلمى', 'ريم', 'دينا', 'ياسمين',
        'منى', 'نهى', 'رانيا', 'إيمان', 'سحر', 'عزة', 'نجوى', 'شيماء', 'هبة', 'زينب',
    ];

    private static array $lastNames = [
        'السيد', 'إبراهيم', 'حسن', 'علي', 'محمد', 'محمود', 'عبدالرحمن', 'يوسف', 'سعيد', 'جاد',
        'الشافعي', 'مراد', 'عوض', 'سلامة', 'شعبان', 'فوزي', 'سليم', 'عبدالله', 'فهمي', 'نصر',
    ];

    private static array $governorates = [
        'القاهرة', 'الجيزة', 'الإسكندرية', 'بورسعيد', 'السويس', 'الأقصر', 'أسوان',
        'قنا', 'سوهاج', 'أسيوط', 'المنيا', 'الفيوم', 'بني سويف', 'الشرقية', 'الدقهلية',
        'الغربية', 'المنوفية', 'البحيرة', 'كفر الشيخ', 'دمياط',
    ];

    private static array $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'];

    private static array $mahramRelations = ['زوج', 'أخ', 'أب', 'ابن'];

    public function definition(): array
    {
        $isMale = fake()->boolean(60);
        $gender = $isMale ? Gender::MALE : Gender::FEMALE;
        $fullName = $this->arabicName($isMale);
        $names = explode(' ', $fullName, 2);
        $firstName = $names[0];
        $lastName = $names[1] ?? self::$lastNames[array_rand(self::$lastNames)];

        return [
            'name' => $fullName,
            'name_en' => fake()->boolean(70) ? strtoupper(fake()->firstName($isMale ? 'male' : 'female').' '.fake()->lastName()) : null,
            'national_id' => $this->generateNationalId(),
            'passport_number' => fake()->boolean(80) ? $this->generatePassport() : null,
            'passport_expiry' => fake()->boolean(80) ? Carbon::now()->addYears(fake()->numberBetween(1, 7)) : null,
            'phone' => $this->egyptianPhone(),
            'phone_alt' => fake()->boolean(30) ? $this->egyptianPhone() : null,
            'email' => fake()->boolean(40) ? fake()->unique()->safeEmail() : null,
            'gender' => $gender,
            'marital_status' => fake()->randomElement(MaritalStatus::cases()),
            'date_of_birth' => Carbon::create(fake()->numberBetween(1950, 2005), fake()->numberBetween(1, 12), fake()->numberBetween(1, 28)),
            'governorate' => fake()->randomElement(self::$governorates),
            'address' => fake()->boolean(60) ? fake()->numberBetween(1, 200).' شارع '.fake()->streetName().' - '.fake()->randomElement(self::$governorates) : null,
            'mahram_name' => $gender === Gender::FEMALE ? $this->arabicName(true) : null,
            'mahram_relation' => $gender === Gender::FEMALE ? fake()->randomElement(self::$mahramRelations) : null,
            'mahram_phone' => $gender === Gender::FEMALE ? $this->egyptianPhone() : null,
            'blood_type' => fake()->boolean(40) ? fake()->randomElement(self::$bloodTypes) : null,
            'medical_notes' => fake()->boolean(15) ? fake()->sentence(4) : null,
        ];
    }

    private function arabicName(bool $male): string
    {
        $first = $male
            ? self::$maleNames[array_rand(self::$maleNames)]
            : self::$femaleNames[array_rand(self::$femaleNames)];

        return $first.' '.self::$lastNames[array_rand(self::$lastNames)];
    }

    private function generateNationalId(): string
    {
        $century = fake()->numberBetween(2, 3);
        $year = fake()->numberBetween(50, 99);
        $month = str_pad(fake()->numberBetween(1, 12), 2, '0', STR_PAD_LEFT);
        $day = str_pad(fake()->numberBetween(1, 28), 2, '0', STR_PAD_LEFT);
        $gov = str_pad(fake()->numberBetween(1, 35), 2, '0', STR_PAD_LEFT);
        $serial = str_pad(fake()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT);

        return $century.$year.$month.$day.$gov.$serial.fake()->randomDigit();
    }

    private function generatePassport(): string
    {
        return 'A'.fake()->numberBetween(10000000, 99999999);
    }

    private function egyptianPhone(): string
    {
        return '01'.fake()->randomElement([0, 1, 2, 5]).fake()->numberBetween(10000000, 99999999);
    }
}
