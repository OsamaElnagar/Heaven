<?php

use App\Models\GalleryItem;
use Livewire\Attributes\Title;
use Livewire\Component;

new
#[Title('معرض الصور')]
class extends Component
{
    public function items()
    {
        return GalleryItem::published()->ordered()->get();
    }
};
