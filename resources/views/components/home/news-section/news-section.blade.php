<section class="bg-white py-16 dark:bg-zinc-950 sm:py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between">
            <div>
                <flux:heading size="xl" class="text-zinc-900 dark:text-white">آخر الأخبار والإعلانات</flux:heading>
                <flux:text class="mt-2 text-zinc-500 dark:text-zinc-400">تابع آخر المستجدات والعروض الخاصة بالحج والعمرة</flux:text>
            </div>
            <a href="{{ route('news.index') }}" wire:navigate class="hidden shrink-0 text-sm font-medium text-emerald-600 hover:underline sm:block dark:text-emerald-400">
                عرض جميع الأخبار
            </a>
        </div>

        @php $allPosts = $this->posts(); @endphp

        @if ($allPosts->isEmpty())
            <div class="mt-12 text-center">
                <div class="mx-auto flex size-20 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800">
                    <flux:icon.newspaper class="size-8 text-zinc-400" />
                </div>
                <h3 class="mt-4 text-lg font-semibold text-zinc-900 dark:text-white">لا توجد أخبار حالياً</h3>
                <p class="mt-1 text-zinc-500 dark:text-zinc-400">لم يتم نشر أي أخبار بعد</p>
            </div>
        @else
            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($allPosts as $post)
                    <a href="{{ route('news.show', $post) }}" wire:navigate
                       class="group overflow-hidden rounded-xl border border-zinc-200 bg-zinc-50 transition hover:shadow-lg dark:border-zinc-800 dark:bg-zinc-900">
                        @if ($post->image)
                            <div class="h-48 overflow-hidden">
                                <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}"
                                     class="h-full w-full object-cover transition duration-300 group-hover:scale-105" />
                            </div>
                        @else
                            <div class="flex h-48 items-center justify-center bg-gradient-to-br from-emerald-800 to-emerald-950">
                                <flux:icon.newspaper class="size-12 text-white/20" />
                            </div>
                        @endif

                        <div class="p-5">
                            <div class="mb-3 flex items-center gap-2 text-xs text-zinc-500 dark:text-zinc-400">
                                <flux:icon.calendar-days class="size-3.5" />
                                <span>{{ $post->published_at?->format('Y/m/d') }}</span>
                            </div>

                            <h3 class="text-lg font-semibold leading-tight text-zinc-900 group-hover:text-emerald-600 dark:text-white dark:group-hover:text-emerald-400">
                                {{ $post->title }}
                            </h3>

                            @if ($post->excerpt)
                                <p class="mt-2 line-clamp-2 text-sm leading-relaxed text-zinc-500 dark:text-zinc-400">
                                    {{ $post->excerpt }}
                                </p>
                            @endif

                            <div class="mt-4 flex items-center gap-1 text-sm font-medium text-emerald-600 dark:text-emerald-400">
                                <span>اقرأ المزيد</span>
                                <flux:icon.arrow-left class="size-4" />
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-8 text-center sm:hidden">
                <a href="{{ route('news.index') }}" wire:navigate class="text-sm font-medium text-emerald-600 hover:underline dark:text-emerald-400">
                    عرض جميع الأخبار
                </a>
            </div>
        @endif
    </div>
</section>
