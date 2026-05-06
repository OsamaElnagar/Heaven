<div class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8" dir="rtl">

    <div class="mb-10 text-center">
        <flux:heading size="2xl" class="mb-3">معرض الصور</flux:heading>
        <p class="text-zinc-500 dark:text-zinc-400">صور من رحلات الحج والعمرة السابقة</p>
    </div>

    @php $galleryItems = $this->items(); @endphp

    @if ($galleryItems->isEmpty())
        <div class="rounded-xl border-2 border-dashed border-zinc-300 py-16 text-center dark:border-zinc-600">
            <flux:icon.photo class="mx-auto size-16 text-zinc-300" />
            <p class="mt-4 text-lg text-zinc-500 dark:text-zinc-400">لا توجد صور في المعرض حالياً</p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($galleryItems as $item)
                <div class="group overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm transition hover:shadow-lg dark:border-zinc-700 dark:bg-zinc-800">
                    @if ($item->hasMedia('gallery'))
                        <div class="h-56 overflow-hidden">
                            <img src="{{ $item->getFirstMediaUrl('gallery') }}"
                                 alt="{{ $item->title }}"
                                 class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                 loading="lazy" />
                        </div>
                    @else
                        <div class="flex h-56 items-center justify-center bg-zinc-100 dark:bg-zinc-800">
                            <flux:icon.photo class="size-12 text-zinc-300" />
                        </div>
                    @endif
                    <div class="p-4 text-center">
                        <p class="font-medium text-zinc-900 dark:text-white">{{ $item->title }}</p>
                        @if ($item->caption)
                            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $item->caption }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
