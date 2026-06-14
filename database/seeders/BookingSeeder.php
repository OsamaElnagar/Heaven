<?php

namespace Database\Seeders;

use App\Enums\BookingChannel;
use App\Models\Agent;
use App\Models\Booking;
use App\Models\Branch;
use App\Models\Client;
use App\Models\Package;
use App\Models\Room;
use App\Models\Trip;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $clients = Client::all();
        $packages = Package::all();
        $trips = Trip::all();
        $rooms = Room::all();
        $branches = Branch::all();
        $agents = Agent::all();

        if ($clients->isEmpty() || $packages->isEmpty()) {
            return;
        }

        foreach ($clients as $client) {
            $count = fake()->numberBetween(1, 3);

            for ($i = 0; $i < $count; $i++) {
                $package = fake()->randomElement($packages);
                $trip = $trips->firstWhere('package_id', $package->id);

                $channel = fake()->randomElement([BookingChannel::DIRECT, BookingChannel::DIRECT, BookingChannel::BRANCH, BookingChannel::AGENT]);
                $branchId = null;
                $agentId = null;

                if ($channel === BookingChannel::BRANCH && $branches->isNotEmpty()) {
                    $branchId = fake()->randomElement($branches)->id;
                } elseif ($channel === BookingChannel::AGENT && $agents->isNotEmpty()) {
                    $agentId = fake()->randomElement($agents)->id;
                }

                $booking = Booking::factory()->create([
                    'client_id' => $client->id,
                    'package_id' => $package->id,
                    'trip_id' => $trip?->id,
                    'room_id' => fake()->boolean(40) && $rooms->isNotEmpty() ? fake()->randomElement($rooms)->id : null,
                    'channel' => $channel,
                    'branch_id' => $branchId,
                    'agent_id' => $agentId,
                ]);

                if ($booking->room_id) {
                    $room = Room::find($booking->room_id);
                    if ($room && $room->occupied < $room->capacity) {
                        $room->increment('occupied');
                    }
                }
            }
        }
    }
}
