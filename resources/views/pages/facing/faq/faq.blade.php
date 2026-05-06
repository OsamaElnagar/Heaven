<div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8" dir="rtl" x-data="{ openFaq: 0 }">

    <div class="mb-10 text-center">
        <flux:heading size="2xl" class="mb-3">الأسئلة الشائعة</flux:heading>
        <p class="text-zinc-500 dark:text-zinc-400">إجابات على أكثر الأسئلة شيوعاً</p>
    </div>

    @php $faqs = $this->faqs(); @endphp

    @if ($faqs->isEmpty())
        <div class="rounded-xl border border-dashed border-zinc-300 py-16 text-center dark:border-zinc-600">
            <flux:icon.question-mark-circle class="mx-auto size-12 text-zinc-300" />
            <p class="mt-4 text-lg text-zinc-500 dark:text-zinc-400">لا توجد أسئلة شائعة حالياً</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($faqs as $index => $faq)
                <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-800"
                    x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }">
                    <button @@click="open = !open" class="flex w-full items-center justify-between gap-4 px-6 py-4 text-right">
                        <span class="text-lg font-semibold text-zinc-900 dark:text-white">{{ $faq->question }}</span>
                        <flux:icon.chevron-down class="size-5 shrink-0 text-zinc-400 transition-transform duration-200"
                            ::class="open ? 'rotate-180' : ''" />
                    </button>
                    <div x-show="open" x-collapse x-cloak>
                        <div
                            class="border-t border-zinc-100 px-6 pb-4 pt-3 leading-relaxed text-zinc-600 dark:border-zinc-800 dark:text-zinc-400">
                            {{ $faq->answer }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>