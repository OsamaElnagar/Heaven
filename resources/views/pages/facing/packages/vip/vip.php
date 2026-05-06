<?php

use App\Enums\PackageGrade;
use App\Models\Package;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new
#[Title('باقات VIP')]
class extends Component
{
    use WithPagination;

    public function packages()
    {
        return Package::query()
            ->where('is_active', true)
            ->whereIn('grade', [PackageGrade::VIP->value, PackageGrade::VVIP->value])
            ->latest('departure_date')
            ->paginate(12);
    }
};
