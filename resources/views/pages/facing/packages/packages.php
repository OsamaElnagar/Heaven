<?php

use App\Enums\PackageGrade;
use App\Models\Package;
use App\Models\PackageType;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new
#[Title('الباقات')]
class extends Component
{
    use WithPagination;

    #[Url]
    public string $type = '';

    #[Url]
    public string $grade = '';

    #[Url]
    public string $year = '';

    #[Url]
    public int $minPrice = 0;

    #[Url]
    public int $maxPrice = 1000000;

    #[Url]
    public int $minDuration = 0;

    #[Url]
    public int $maxDuration = 60;

    public function types(): array
    {
        return PackageType::all()->map(fn ($t) => ['value' => (string) $t->id, 'label' => $t->name_ar])->toArray();
    }

    public function grades(): array
    {
        return collect(PackageGrade::cases())->map(fn ($g) => ['value' => $g->value, 'label' => $g->getLabel()])->toArray();
    }

    public function years(): array
    {
        $current = (int) now()->year;
        $years = [];
        for ($y = $current - 1; $y <= $current + 1; $y++) {
            $years[] = ['value' => $y, 'label' => (string) $y];
        }

        return $years;
    }

    public function packages()
    {
        return Package::query()
            ->where('is_active', true)
            ->when($this->type, fn ($q) => $q->whereHas('type', fn ($sub) => $sub->where('slug', $this->type)->orWhere('id', $this->type)))
            ->when($this->grade, fn ($q) => $q->where('grade', $this->grade))
            ->when($this->year, fn ($q) => $q->where('season_year', $this->year))
            ->when($this->minPrice > 0, fn ($q) => $q->where('base_price', '>=', $this->minPrice))
            ->when($this->maxPrice < 1_000_000, fn ($q) => $q->where('base_price', '<=', $this->maxPrice))
            ->when($this->minDuration > 0, fn ($q) => $q->where('duration_nights', '>=', $this->minDuration))
            ->when($this->maxDuration < 60, fn ($q) => $q->where('duration_nights', '<=', $this->maxDuration))
            ->orderBy('departure_date')
            ->paginate(12);
    }

    public function resetFilters(): void
    {
        $this->reset(['type', 'grade', 'year', 'minPrice', 'maxPrice', 'minDuration', 'maxDuration']);
    }
};
