<div class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8" dir="rtl">

    <div class="mb-10 text-center">
        <flux:heading size="2xl" class="mb-3">الأخبار والإعلانات</flux:heading>
        <p class="text-zinc-500 dark:text-zinc-400">تابع آخر أخبار الحج والعمرة وإعلانات الشركة</p>
    </div>

    @php $posts = $this->posts(); @endphp

    @if ($posts->isEmpty())
        <div class="rounded-xl border border-dashed border-zinc-300 py-16 text-center dark:border-zinc-600">
            <flux:icon.newspaper class="mx-auto size-12 text-zinc-300" />
            <p class="mt-4 text-lg text-zinc-500 dark:text-zinc-400">لا توجد أخبار حالياً</p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($posts as $post)
                <a href="{{ route('news.show', $post) }}" wire:navigate
                   class="group block overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm transition hover:shadow-md dark:border-zinc-700 dark:bg-zinc-800">
                    <div class="flex h-48 items-center justify-center bg-zinc-100 dark:bg-zinc-800">
                        <flux:icon.newspaper class="size-12 text-zinc-300" />
                    </div>
                    <div class="p-5">
                        <div class="mb-2 flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
                            <flux:icon.calendar-days class="size-4" />
                            <span>{{ $post->published_at->format('Y/m/d') }}</span>
                        </div>
                        <h3 class="mb-2 text-lg font-bold text-zinc-900 group-hover:text-emerald-600 dark:text-white">
                            {{ $post->title }}
                        </h3>
                        @if ($post->excerpt)
                            <p class="text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">
                                {{ Str::limit($post->excerpt, 120) }}
                            </p>
                        @endif
                        <div class="mt-4">
                            <span class="inline-flex items-center gap-1 text-sm font-medium text-emerald-600 transition group-hover:gap-2 dark:text-emerald-400">
                                اقرأ المزيد
                                <flux:icon.arrow-right class="size-4" />
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $posts->links() }}
        </div>
    @endif

</div>
