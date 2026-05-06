<div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8" dir="rtl">

    {{-- Breadcrumb --}}
    <nav class="mb-8 flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
        <a href="{{ route('home') }}" wire:navigate class="hover:text-zinc-700 dark:hover:text-zinc-300">الرئيسية</a>
        <flux:icon.arrow-right class="size-3 rotate-180" />
        <a href="{{ route('news.index') }}" wire:navigate
            class="hover:text-zinc-700 dark:hover:text-zinc-300">الأخبار</a>
        <flux:icon.arrow-right class="size-3 rotate-180" />
        <span class="text-zinc-900 dark:text-white">{{ $post->title }}</span>
    </nav>

    {{-- Post Header --}}
    <div class="mb-8">
        <div class="mb-4 flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
            <flux:icon.calendar-days class="size-4" />
            <span>{{ $post->published_at->format('Y/m/d') }}</span>
        </div>
        <flux:heading size="2xl" class="mb-6">{{ $post->title }}</flux:heading>

        <div class="flex h-64 items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-800">
            <flux:icon.newspaper class="size-16 text-zinc-300" />
        </div>
    </div>

    {{-- Post Content --}}
    <div
        class="prose max-w-none rounded-xl border border-zinc-200 bg-white p-6 shadow-sm leading-relaxed dark:border-zinc-700 dark:bg-zinc-800">
        {!! nl2br(e($post->content)) !!}
    </div>

    {{-- Back Link --}}
    <div class="mt-10 text-center">
        <a href="{{ route('news.index') }}" wire:navigate
            class="inline-flex items-center gap-2 text-sm font-medium text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300">
            <flux:icon.arrow-right class="size-4" />
            العودة للأخبار
        </a>
    </div>

</div>