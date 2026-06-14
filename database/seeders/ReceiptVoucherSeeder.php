<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Enums\ExpenseStatus;
use App\Enums\PayerType;
use App\Enums\PaymentType;
use App\Enums\VoucherPaymentMethod;
use App\Models\BankAccount;
use App\Models\Booking;
use App\Models\ReceiptVoucher;
use App\Models\Safe;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReceiptVoucherSeeder extends Seeder
{
    public function run(): void
    {
        $safe = Safe::where('is_active', true)->first();
        $bank = BankAccount::where('is_active', true)->first();
        $cashTarget = $safe ?? $bank;

        if (! $cashTarget) {
            return;
        }

        $bookings = Booking::whereNotIn('status', [
            BookingStatus::CANCELLED->value,
            BookingStatus::REFUNDED->value,
        ])->get();

        $paymentTypes = [
            PaymentType::DEPOSIT,
            PaymentType::INSTALLMENT,
            PaymentType::INSTALLMENT,
            PaymentType::FINAL,
        ];

        foreach ($bookings as $booking) {
            $paymentCount = fake()->numberBetween(1, 3);
            $totalPaid = 0;
            $vouchersCreated = 0;

            for ($i = 0; $i < $paymentCount; $i++) {
                $remaining = $booking->net_price - $totalPaid;
                if ($remaining <= 0) {
                    break;
                }

                $amount = $i === $paymentCount - 1
                    ? (int) $remaining
                    : fake()->numberBetween(1000, (int) max($remaining * 0.6, 2000));

                $totalPaid += $amount;

                $useSafe = $i % 2 === 0 && $safe;
                $method = $useSafe ? VoucherPaymentMethod::SAFE : VoucherPaymentMethod::BANK;

                ReceiptVoucher::create([
                    'voucher_date' => now()->subDays(fake()->numberBetween(0, 30)),
                    'receipt_method' => $method,
                    'safe_id' => $useSafe ? $safe->id : null,
                    'bank_account_id' => $useSafe ? null : ($bank?->id),
                    'amount' => $amount,
                    'payment_type' => fake()->randomElement($paymentTypes),
                    'payer_type' => PayerType::CLIENT,
                    'client_id' => $booking->client_id,
                    'booking_id' => $booking->id,
                    'description' => 'دفعة على الحجز #'.$booking->id,
                    'reference' => 'SEED-'.str_pad((string) $booking->id, 5, '0', STR_PAD_LEFT).'-'.($i + 1),
                    'status' => ExpenseStatus::POSTED,
                    'created_by' => User::inRandomOrder()->first()->id,
                ]);
                $vouchersCreated++;
            }

            if ($vouchersCreated > 0) {
                $netPaid = (float) $totalPaid;
                $booking->updateQuietly([
                    'paid_amount' => $netPaid,
                    'status' => $netPaid > 0 ? BookingStatus::CONFIRMED : BookingStatus::PENDING,
                ]);
            }
        }
    }
}
