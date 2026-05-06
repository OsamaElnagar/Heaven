<?php

use App\Models\Package;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new
#[Title('رحلات المجموعات')]
class extends Component
{
    use WithPagination;

    public function packages()
    {
        return Package::query()
            ->where('is_active', true)
            ->whereRaw('total_seats - reserved_seats > 10')
            ->latest('departure_date')
            ->paginate(12);
    }
};
