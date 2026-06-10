<?php

use App\Enums\BookingStatus;
use App\Enums\FiscalYearStatus;
use App\Enums\RoomType;
use App\Models\Booking;
use App\Models\Client;
use App\Models\FiscalYear;
use App\Models\Hotel;
use App\Models\Package;
use App\Models\Room;
use App\Models\Supplier;
use App\Models\Trip;
use App\Services\BookingService;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\assertDatabaseHas;

uses(RefreshDatabase::class);

beforeEach(function () {
    FiscalYear::create([
        'name' => now()->year,
        'starts_at' => now()->startOfYear(),
        'ends_at' => now()->endOfYear(),
        'status' => FiscalYearStatus::OPEN,
    ]);

    $this->supplier = Supplier::factory()->create();
    $this->hotel = Hotel::factory()->for($this->supplier)->create();
    $this->package = Package::factory()->create(['total_seats' => 40, 'reserved_seats' => 0]);
    $this->trip = Trip::factory()->for($this->package)->create();
});

function makeRoom(Hotel $hotel, Trip $trip, array $overrides = []): Room
{
    return Room::factory()->for($hotel)->for($trip)->create($overrides);
}

function makeBooking(Client $client, Package $package, array $overrides = []): Booking
{
    return Booking::factory()->pending()->for($client)->for($package)->create($overrides);
}

it('creates a booking with correct defaults', function () {
    $client = Client::factory()->create();

    $service = app(BookingService::class);
    $booking = $service->createBooking($client, $this->package, [
        'room_type' => RoomType::DOUBLE,
        'total_price' => $this->package->base_price,
    ]);

    expect($booking)->toBeInstanceOf(Booking::class)
        ->and($booking->client_id)->toBe($client->id)
        ->and($booking->package_id)->toBe($this->package->id)
        ->and($booking->status)->toBe(BookingStatus::PENDING);

    assertDatabaseHas(Booking::class, [
        'id' => $booking->id,
        'client_id' => $client->id,
    ]);
});

it('throws when no seats are available', function () {
    $client = Client::factory()->create();
    $fullPackage = Package::factory()->create(['total_seats' => 2, 'reserved_seats' => 2]);

    app(BookingService::class)->createBooking($client, $fullPackage, [
        'room_type' => RoomType::SINGLE,
        'total_price' => 100000,
    ]);
})->throws(InvalidArgumentException::class, 'No available seats on this package.');

it('cancels a booking with reason appended to notes', function () {
    $client = Client::factory()->create();
    $booking = makeBooking($client, $this->package, ['notes' => 'Existing note']);

    app(BookingService::class)->cancelBooking($booking, 'Client changed mind');

    $booking->refresh();

    expect($booking->status)->toBe(BookingStatus::CANCELLED)
        ->and($booking->notes)->toContain('Existing note')
        ->and($booking->notes)->toContain('Cancelled: Client changed mind');
});

it('cancels a booking without reason preserves notes', function () {
    $client = Client::factory()->create();
    $booking = makeBooking($client, $this->package, ['notes' => 'Original']);

    app(BookingService::class)->cancelBooking($booking);

    expect($booking->refresh()->notes)->toBe('Original');
});

it('assigns a room and increments occupied count', function () {
    $room = makeRoom($this->hotel, $this->trip, ['capacity' => 4, 'occupied' => 1]);
    $client = Client::factory()->create();
    $booking = makeBooking($client, $this->package);

    app(BookingService::class)->assignRoom($booking, $room);

    expect($booking->refresh()->room_id)->toBe($room->id);
    expect($room->fresh()->occupied)->toBe(2);
});

it('throws when assigning a full room', function () {
    $room = makeRoom($this->hotel, $this->trip, ['capacity' => 2, 'occupied' => 2]);
    $client = Client::factory()->create();
    $booking = makeBooking($client, $this->package);

    app(BookingService::class)->assignRoom($booking, $room);
})->throws(InvalidArgumentException::class, 'Room is at full capacity.');

it('decrements old room occupied count when reassigning', function () {
    $oldRoom = makeRoom($this->hotel, $this->trip, ['capacity' => 4, 'occupied' => 2]);
    $newRoom = makeRoom($this->hotel, $this->trip, ['capacity' => 4, 'occupied' => 1]);
    $client = Client::factory()->create();
    $booking = makeBooking($client, $this->package, ['room_id' => $oldRoom->id]);

    app(BookingService::class)->assignRoom($booking, $newRoom);

    expect($oldRoom->fresh()->occupied)->toBe(1);
    expect($newRoom->fresh()->occupied)->toBe(2);
    expect($booking->refresh()->room_id)->toBe($newRoom->id);
});

it('unassigns room and decrements occupied count', function () {
    $room = makeRoom($this->hotel, $this->trip, ['capacity' => 4, 'occupied' => 2]);
    $client = Client::factory()->create();
    $booking = makeBooking($client, $this->package, ['room_id' => $room->id]);

    app(BookingService::class)->unassignRoom($booking);

    expect($booking->refresh()->room_id)->toBeNull();
    expect($room->fresh()->occupied)->toBe(1);
});

it('does nothing when unassigning a booking with no room', function () {
    $client = Client::factory()->create();
    $booking = makeBooking($client, $this->package, ['room_id' => null]);

    app(BookingService::class)->unassignRoom($booking);

    expect($booking->refresh()->room_id)->toBeNull();
});

it('does not decrement occupied below zero', function () {
    $room = makeRoom($this->hotel, $this->trip, ['capacity' => 4, 'occupied' => 0]);
    $client = Client::factory()->create();
    $booking = makeBooking($client, $this->package, ['room_id' => $room->id]);

    app(BookingService::class)->unassignRoom($booking);

    expect($room->fresh()->occupied)->toBe(0);
});

it('calculates pricing for single room type', function () {
    $pricing = app(BookingService::class)->calculatePricing($this->package, RoomType::SINGLE);

    expect($pricing['base_price'])->toBe((float) $this->package->base_price)
        ->and($pricing['room_surcharge'])->toBe((float) $this->package->base_price * 0.5)
        ->and($pricing['discount'])->toBe(0.0)
        ->and($pricing['net_price'])->toBe((float) $this->package->base_price * 1.5);
});

it('calculates pricing for double room type with zero surcharge', function () {
    $pricing = app(BookingService::class)->calculatePricing($this->package, RoomType::DOUBLE);

    expect($pricing['room_surcharge'])->toBe(0)
        ->and($pricing['net_price'])->toBe((float) $this->package->base_price);
});

it('calculates pricing for sextuple room type with maximum discount', function () {
    $basePrice = (float) $this->package->base_price;
    $pricing = app(BookingService::class)->calculatePricing($this->package, RoomType::SEXTUPLE, 5000);

    expect($pricing['room_surcharge'])->toBe(-$basePrice * 0.4)
        ->and($pricing['net_price'])->toBe(max($basePrice - $basePrice * 0.4 - 5000, 0));
});

it('ensures net price never goes below zero', function () {
    $pricing = app(BookingService::class)->calculatePricing($this->package, RoomType::SEXTUPLE, 999999);

    expect($pricing['net_price'])->toBe(0);
});
