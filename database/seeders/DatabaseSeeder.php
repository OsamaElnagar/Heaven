<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Models\Package;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public bool $production = false;

    public function run(): void
    {
        $this->production = $this->production || in_array('--production', $_SERVER['argv'] ?? []);

        $this->call([
            ChartOfAccountsSeeder::class,
            FiscalYearSeeder::class,
            FaqSeeder::class,
            PostSeeder::class,
            GalleryItemSeeder::class,
            UserSeeder::class,
            SafeSeeder::class,
            SupplierSeeder::class,
            BranchSeeder::class,
            AgentSeeder::class,
            EmployeeSeeder::class,
            ClientSeeder::class,
        ]);

        if ($this->production) {
            return;
        }

        $this->call([
            HotelSeeder::class,
            PackageSeeder::class,
            TripSeeder::class,
            PackageHotelSeeder::class,
            RoomSeeder::class,
            BookingSeeder::class,
            ReceiptVoucherSeeder::class,
            VisaSeeder::class,
            ExpenseSeeder::class,
        ]);

        $this->fixReservedSeats();
    }

    private function fixReservedSeats(): void
    {
        $packages = Package::whereHas('bookings')->cursor();

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
