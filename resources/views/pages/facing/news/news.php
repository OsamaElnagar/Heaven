<?php

use App\Models\Post;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new
#[Title('الأخبار والإعلانات')]
class extends Component
{
    use WithPagination;

    public function posts()
    {
        return Post::published()
            ->latest('published_at')
            ->paginate(9);
    }
};
