<?php

use App\Enums\BookingStatus;
use App\Enums\FiscalYearStatus;
use App\Models\Booking;
use App\Models\Client;
use App\Models\FiscalYear;
use App\Models\Package;
use App\Models\Visa;
use App\Observers\BookingObserver;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    FiscalYear::create([
        'name' => now()->year,
        'starts_at' => now()->startOfYear(),
        'ends_at' => now()->endOfYear(),
        'status' => FiscalYearStatus::OPEN,
    ]);
});

it('generates a reference on creating', function () {
    $client = Client::factory()->create();
    $package = Package::factory()->create();
    $booking = Booking::factory()->pending()->for($client)->for($package)->create([
        'reference' => null,
    ]);

    expect($booking->reference)->toMatch('/^BK-\d{4}-\d{5}$/');
});

it('generates sequential references', function () {
    $client = Client::factory()->create();
    $package = Package::factory()->create();
    $b1 = Booking::factory()->pending()->for($client)->for($package)->create(['reference' => null]);
    $b2 = Booking::factory()->pending()->for($client)->for($package)->create(['reference' => null]);

    $seq1 = (int) substr($b1->reference, -5);
    $seq2 = (int) substr($b2->reference, -5);

    expect($seq2)->toBe($seq1 + 1);
});

it('computes net_price on creating when null', function () {
    $client = Client::factory()->create();
    $package = Package::factory()->create();
    $booking = Booking::factory()->pending()->for($client)->for($package)->create([
        'total_price' => 100000,
        'discount' => 15000,
        'net_price' => null,
    ]);

    expect($booking->net_price)->toBe(85000.0);
});

it('recalculates net_price on updating total_price or discount', function () {
    $client = Client::factory()->create();
    $package = Package::factory()->create();
    $booking = Booking::factory()->pending()->for($client)->for($package)->create([
        'total_price' => 100000,
        'discount' => 10000,
        'net_price' => 90000,
    ]);

    $booking->update(['total_price' => 120000]);

    expect($booking->refresh()->net_price)->toEqual(110000.0);
});

it('defaults status to PENDING when null', function () {
    $client = Client::factory()->create();
    $package = Package::factory()->create();
    $booking = Booking::factory()->for($client)->for($package)->create(['status' => null]);

    expect($booking->status)->toBe(BookingStatus::PENDING);
});

it('increments reserved_seats when status changes to confirmed', function () {
    $package = Package::factory()->create(['reserved_seats' => 5]);
    $client = Client::factory()->create();
    $booking = Booking::factory()->pending()->for($client)->for($package)->create();

    $booking->update(['status' => BookingStatus::CONFIRMED]);

    expect($package->fresh()->reserved_seats)->toBe(6);
});

it('creates visa record when status changes to confirmed', function () {
    $package = Package::factory()->create();
    $client = Client::factory()->create();
    $booking = Booking::factory()->pending()->for($client)->for($package)->create();

    $booking->update(['status' => BookingStatus::CONFIRMED]);

    expect(Visa::where('booking_id', $booking->id)->exists())->toBeTrue();
});

it('does not create duplicate visa when one already exists', function () {
    $package = Package::factory()->create();
    $client = Client::factory()->create();
    $booking = Booking::factory()->pending()->for($client)->for($package)->create();
    Visa::factory()->create(['booking_id' => $booking->id]);

    $booking->update(['status' => BookingStatus::CONFIRMED]);

    expect(Visa::where('booking_id', $booking->id)->count())->toBe(1);
});

it('decrements reserved_seats when status changes from confirmed to cancelled', function () {
    $package = Package::factory()->create(['reserved_seats' => 5]);
    $client = Client::factory()->create();
    $booking = Booking::factory()->pending()->for($client)->for($package)->create();

    // PENDING → CONFIRMED: increments reserved_seats to 6
    $booking->update(['status' => BookingStatus::CONFIRMED]);
    expect($package->fresh()->reserved_seats)->toBe(6);

    // CONFIRMED → CANCELLED: decrements reserved_seats back to 5
    $booking->update(['status' => BookingStatus::CANCELLED]);
    expect($package->fresh()->reserved_seats)->toBe(5);
});

it('floors reserved_seats at zero on cancellation', function () {
    $package = Package::factory()->create(['reserved_seats' => 0]);
    $client = Client::factory()->create();
    $booking = Booking::factory()->confirmed()->for($client)->for($package)->create();

    $booking->update(['status' => BookingStatus::CANCELLED]);

    expect($package->fresh()->reserved_seats)->toBe(0);
});

it('does not decrement reserved_seats when transitioning non-confirmed to cancelled', function () {
    $package = Package::factory()->create(['reserved_seats' => 5]);
    $client = Client::factory()->create();
    $booking = Booking::factory()->pending()->for($client)->for($package)->create();

    $booking->update(['status' => BookingStatus::CANCELLED]);

    expect($package->fresh()->reserved_seats)->toBe(5);
});

it('recalculates paid_amount from receipt vouchers', function () {
    $client = Client::factory()->create();
    $package = Package::factory()->create();
    $booking = Booking::factory()->pending()->for($client)->for($package)->create([
        'paid_amount' => 0,
    ]);

    app(BookingObserver::class)->recalculatePaidAmount($booking);

    $booking->refresh();

    expect($booking->paid_amount)->toBe(0);
    expect($booking->status)->toBe(BookingStatus::PENDING);
});
