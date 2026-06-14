<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Enums\VisaStatus;
use App\Models\Booking;
use App\Models\Visa;
use Illuminate\Database\Seeder;

class VisaSeeder extends Seeder
{
    public function run(): void
    {
        $bookings = Booking::where('status', BookingStatus::CONFIRMED)
            ->whereDoesntHave('visa')
            ->get();

        foreach ($bookings as $booking) {
            $random = fake()->numberBetween(1, 10);
            $visaData = ['booking_id' => $booking->id];

            if ($random <= 3) {
                $visaData['status'] = VisaStatus::NOT_APPLIED;
            } elseif ($random <= 6) {
                $visaData['status'] = VisaStatus::APPLIED;
                $visaData['applied_at'] = now()->subDays(fake()->numberBetween(1, 14));
            } elseif ($random <= 9) {
                $visaData['status'] = VisaStatus::APPROVED;
                $visaData['applied_at'] = now()->subDays(fake()->numberBetween(15, 45));
                $visaData['approved_at'] = now()->subDays(fake()->numberBetween(1, 10));
                $visaData['expiry_date'] = now()->addMonths(3);
                $visaData['visa_number'] = fake()->numerify('VSA-##########');
            } else {
                $visaData['status'] = VisaStatus::REJECTED;
                $visaData['applied_at'] = now()->subDays(fake()->numberBetween(10, 30));
                $visaData['rejection_reason'] = 'بيانات غير مكتملة - يرجى إعادة التقديم';
            }

            Visa::create($visaData);
        }
    }
}
