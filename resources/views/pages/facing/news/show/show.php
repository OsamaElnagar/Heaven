<?php

use App\Models\Post;
use Livewire\Attributes\Title;
use Livewire\Component;

new
#[Title('تفاصيل الخبر')]
class extends Component
{
    public Post $post;

    public function mount(Post $post): void
    {
        if (! $post->is_published || ! $post->published_at || $post->published_at->isFuture()) {
            abort(404);
        }
    }
};
