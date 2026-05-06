<?php

use App\Models\Post;
use Livewire\Component;

new class extends Component
{
    public function posts()
    {
        return Post::published()->latest('published_at')->limit(3)->get();
    }
};
