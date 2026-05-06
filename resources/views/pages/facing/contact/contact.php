<?php

use Livewire\Attributes\Title;
use Livewire\Component;

new
#[Title('اتصل بنا')]
class extends Component
{
    public string $name = '';

    public string $phone = '';

    public string $email = '';

    public string $message = '';

    public bool $sent = false;

    public function submit(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $this->sent = true;
        $this->reset(['name', 'phone', 'email', 'message']);
    }
};
