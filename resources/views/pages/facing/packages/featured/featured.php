<?php

use App\Models\Package;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new
#[Title('العروض المميزة')]
class extends Component
{
    use WithPagination;

    public function packages()
    {
        return Package::query()
            ->where('is_active', true)
            ->where('season_year', now()->year)
            ->orderBy('departure_date')
            ->paginate(12);
    }
};
