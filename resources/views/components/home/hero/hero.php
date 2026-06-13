<?php

use App\Enums\PackageGrade;
use App\Models\PackageType;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public string $searchType = '';

    public string $searchYear = '';

    public string $searchGrade = '';

    #[Computed]
    public function types(): array
    {
        return PackageType::all()->map(fn ($t) => ['value' => (string) $t->id, 'label' => $t->name_ar])->toArray();
    }

    #[Computed]
    public function grades(): array
    {
        return collect(PackageGrade::cases())->map(fn ($g) => ['value' => $g->value, 'label' => $g->getLabel()])->toArray();
    }

    #[Computed]
    public function years(): array
    {
        $current = (int) now()->year;

        return [
            ['value' => (string) $current, 'label' => (string) $current],
            ['value' => (string) ($current + 1), 'label' => (string) ($current + 1)],
        ];
    }

    public function search(): void
    {
        $params = array_filter([
            'type' => $this->searchType,
            'year' => $this->searchYear,
            'grade' => $this->searchGrade,
        ]);

        $this->redirect(route('packages.index', $params), navigate: true);
    }
};
