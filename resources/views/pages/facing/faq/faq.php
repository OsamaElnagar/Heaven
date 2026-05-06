<?php

use App\Models\Faq;
use Livewire\Attributes\Title;
use Livewire\Component;

new
#[Title('الأسئلة الشائعة')]
class extends Component
{
    public function faqs()
    {
        return Faq::published()->ordered()->get();
    }
};
