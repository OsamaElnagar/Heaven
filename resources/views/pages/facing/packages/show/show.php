<?php

use App\Models\Package;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new
#[Title('تفاصيل الباقة')]
#[Layout('layouts::app')]
class extends Component
{
    public Package $package;

    public function mount(Package $package): void
    {
        $this->package = $package->load(['hotels', 'trips']);
    }

    public function availableSeats(): int
    {
        return $this->package->total_seats - $this->package->reserved_seats;
    }

    public function singlePrice(): float
    {
        return $this->package->base_price * 2;
    }

    public function doublePrice(): float
    {
        return $this->package->base_price * 1.3;
    }

    public function triplePrice(): float
    {
        return $this->package->base_price * 1;
    }

    public function quadPrice(): float
    {
        return $this->package->base_price * 0.8;
    }
};
