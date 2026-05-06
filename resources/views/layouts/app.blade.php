<x-layouts::app.header :title="$title ?? null">
    <flux:main class="{{ request()->is('settings*') ? '' : 'p-0! m-0!' }}">
        {{ $slot }}
        <livewire:footer />
    </flux:main>
</x-layouts::app.header>