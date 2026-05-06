<?php

use App\Enums\RoomType;
use App\Models\Booking;
use App\Models\Client;
use App\Models\Package;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;

new
#[Title('احجز الآن')]
class extends Component
{
    public Package $package;

    public string $name = '';

    public string $phone = '';

    public string $nationalId = '';

    public string $email = '';

    public int $travelersCount = 1;

    public string $preferredRoomType = '';

    public string $notes = '';

    public bool $submitted = false;

    public ?string $bookingReference = null;

    public function mount(Package $package): void
    {
        $this->package = $package;
    }

    public function roomTypes(): array
    {
        return collect(RoomType::cases())
            ->filter(fn ($r) => in_array($r->value, ['single', 'double', 'triple', 'quad']))
            ->map(fn ($r) => ['value' => $r->value, 'label' => $r->getLabel()])
            ->toArray();
    }

    public function submit(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'nationalId' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'travelersCount' => ['required', 'integer', 'min:1', 'max:20'],
            'preferredRoomType' => ['required', 'in:single,double,triple,quad'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $client = Client::firstOrCreate(
            ['national_id' => $this->nationalId],
            [
                'name' => $this->name,
                'phone' => $this->phone,
                'email' => $this->email ?: null,
            ]
        );

        if (! $client->wasRecentlyCreated) {
            $client->update([
                'name' => $this->name,
                'phone' => $this->phone,
                'email' => $this->email ?: $client->email,
            ]);
        }

        $multiplier = match ($this->preferredRoomType) {
            'single' => 2.0,
            'double' => 1.3,
            'triple' => 1.0,
            'quad' => 0.8,
        };
        $totalPrice = $this->package->base_price * $multiplier * $this->travelersCount;

        $reference = 'HVN-'.strtoupper(Str::random(8));

        $booking = Booking::create([
            'reference' => $reference,
            'client_id' => $client->id,
            'package_id' => $this->package->id,
            'status' => 'pending',
            'room_type' => $this->preferredRoomType,
            'total_price' => $totalPrice,
            'net_price' => $totalPrice,
            'paid_amount' => 0,
            'notes' => "عدد المسافرين: {$this->travelersCount}\n".$this->notes,
        ]);

        $this->package->increment('reserved_seats', $this->travelersCount);

        $this->bookingReference = $reference;
        $this->submitted = true;
    }
};
