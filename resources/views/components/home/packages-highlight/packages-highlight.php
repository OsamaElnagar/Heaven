<?php

use App\Models\Package;
use Livewire\Component;

new class extends Component
{
    public function packages()
    {
        return Package::query()
            ->where('is_active', true)
            ->with('hotels')
            ->latest('departure_date')
            ->limit(6)
            ->get();
    }
};
