<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Enums\VisaStatus;
use App\Models\Booking;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\Hotel;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Supplier;
use App\Models\Trip;
use App\Models\User;
use App\Models\Visa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedUsers();
        $suppliers = $this->seedSuppliers();
        $hotels = $this->seedHotels($suppliers);
        $this->seedEmployees();
        $packages = $this->seedPackages();
        $this->seedPackageHotels($packages, $hotels);
        $trips = $this->seedTrips($packages);
        $rooms = $this->seedRooms($hotels, $trips);
        $clients = $this->seedClients();
        $bookings = $this->seedBookings($clients, $packages, $trips, $rooms);
        $this->seedPayments($bookings);
        $this->handleVisaStatuses($bookings);
        $this->seedExpenses($trips);
        $this->fixReservedSeats($packages);
    }

    private function seedUsers(): User
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@heaven.test'],
            ['name' => 'أسامة سيد', 'password' => bcrypt('password')]
        );

        User::factory(2)->create();

        return $admin;
    }

    private function seedSuppliers(): array
    {
        return Supplier::factory(8)->create()->all();
    }

    private function seedHotels(array $suppliers): array
    {
        $hotels = collect();

        $makkahSupplier = $suppliers[0];
        $madinahSupplier = $suppliers[1];

        for ($i = 0; $i < 5; $i++) {
            $hotels->push(Hotel::factory()->makkah()->create(['supplier_id' => $makkahSupplier->id]));
        }

        for ($i = 0; $i < 3; $i++) {
            $hotels->push(Hotel::factory()->madinah()->create(['supplier_id' => $madinahSupplier->id]));
        }

        return $hotels->all();
    }

    private function seedEmployees(): void
    {
        Employee::factory(7)->create();
    }

    private function seedPackages(): array
    {
        return Package::factory(8)->create()->all();
    }

    private function seedPackageHotels(array $packages, array $hotels): void
    {
        foreach ($packages as $package) {
            $selectedHotels = collect($hotels)->random(fake()->numberBetween(1, 3));
            foreach ($selectedHotels as $hotel) {
                $package->hotels()->attach($hotel->id, [
                    'city' => $hotel->city,
                    'nights' => fake()->numberBetween(3, 7),
                    'cost_per_person' => fake()->numberBetween(1000, 5000),
                ]);
            }
        }
    }

    private function seedTrips(array $packages): array
    {
        $trips = collect();

        foreach ($packages as $package) {
            $count = fake()->numberBetween(1, 2);
            for ($i = 0; $i < $count; $i++) {
                $trips->push(Trip::factory()->create([
                    'package_id' => $package->id,
                    'departure_at' => $package->departure_date,
                    'return_at' => $package->return_date,
                ]));
            }
        }

        return $trips->all();
    }

    private function seedRooms(array $hotels, array $trips): array
    {
        $rooms = collect();

        foreach ($trips as $trip) {
            $tripHotels = collect($hotels)->random(fake()->numberBetween(1, 2));
            foreach ($tripHotels as $hotel) {
                $roomCount = fake()->numberBetween(3, 8);
                for ($i = 0; $i < $roomCount; $i++) {
                    $rooms->push(Room::factory()->create([
                        'hotel_id' => $hotel->id,
                        'trip_id' => $trip->id,
                    ]));
                }
            }
        }

        return $rooms->all();
    }

    private function seedClients(): array
    {
        return Client::factory(12)->create()->all();
    }

    private function seedBookings(array $clients, array $packages, array $trips, array $rooms): array
    {
        $bookings = collect();

        foreach ($clients as $client) {
            $count = fake()->numberBetween(1, 3);
            for ($i = 0; $i < $count; $i++) {
                $package = fake()->randomElement($packages);
                $trip = collect($trips)->firstWhere('package_id', $package->id);

                $booking = Booking::factory()->create([
                    'client_id' => $client->id,
                    'package_id' => $package->id,
                    'trip_id' => $trip ? $trip->id : null,
                    'room_id' => fake()->boolean(40) ? fake()->randomElement($rooms)->id : null,
                ]);

                if ($booking->room_id) {
                    $room = Room::find($booking->room_id);
                    if ($room && $room->occupied < $room->capacity) {
                        $room->increment('occupied');
                    }
                }

                $bookings->push($booking);
            }
        }

        return $bookings->all();
    }

    private function seedPayments(array $bookings): void
    {
        foreach ($bookings as $booking) {
            if (in_array($booking->status->value, ['cancelled', 'refunded'], true)) {
                continue;
            }

            $paymentCount = fake()->numberBetween(1, 3);
            $totalPaid = 0;

            for ($i = 0; $i < $paymentCount; $i++) {
                $remaining = $booking->net_price - $totalPaid;
                $amount = $i === $paymentCount - 1
                    ? $remaining
                    : fake()->numberBetween(1000, (int) max($remaining * 0.6, 2000));

                $totalPaid += $amount;

                Payment::factory()->create([
                    'booking_id' => $booking->id,
                    'amount' => $amount,
                    'received_by' => User::inRandomOrder()->first()->id,
                ]);
            }

            Booking::withoutEvents(function () use ($booking) {
                $paid = $booking->payments()
                    ->whereNot('type', 'refund')
                    ->sum('amount');
                $refunded = $booking->payments()
                    ->where('type', 'refund')
                    ->sum('amount');
                $netPaid = (float) $paid - (float) $refunded;

                $booking->updateQuietly([
                    'paid_amount' => $netPaid,
                    'status' => $netPaid > 0 ? BookingStatus::CONFIRMED : BookingStatus::PENDING,
                ]);
            });
        }
    }

    private function handleVisaStatuses(array $bookings): void
    {
        foreach ($bookings as $booking) {
            if ($booking->status !== BookingStatus::CONFIRMED) {
                continue;
            }

            if ($booking->visa) {
                continue;
            }

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

    private function seedExpenses(array $trips): void
    {
        foreach ($trips as $trip) {
            $count = fake()->numberBetween(1, 4);
            for ($i = 0; $i < $count; $i++) {
                Expense::factory()->create([
                    'trip_id' => $trip->id,
                    'paid_by' => User::inRandomOrder()->first()->id,
                ]);
            }
        }
    }

    private function fixReservedSeats(array $packages): void
    {
        foreach ($packages as $package) {
            $count = $package->bookings()
                ->where('status', BookingStatus::CONFIRMED)
                ->count();

            DB::table('packages')
                ->where('id', $package->id)
                ->update(['reserved_seats' => $count]);
        }
    }
}
