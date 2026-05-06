<section class="relative flex min-h-[85vh] items-center justify-center overflow-hidden bg-zinc-50 dark:bg-zinc-950">
    {{-- Light mode gradient --}}
    <div
        class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-emerald-100/60 via-zinc-50 to-zinc-50 dark:hidden">
    </div>
    {{-- Dark mode gradient --}}
    <div
        class="absolute inset-0 hidden bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-emerald-900/30 via-zinc-950 to-zinc-950 dark:block">
    </div>

    {{-- Pattern overlay --}}
    <div
        class="absolute inset-0 opacity-[0.03] dark:opacity-30 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmZmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSI+PHBhdGggZD0iTTM2IDM0di00aC0ydjRoLTR2Mmg0djRoMnYtNGg0di0yaC00em0wLTMwVjBoLTJ2NGgtNHYyaDR2NGgyVjZoNFY0aC00ek02IDM0di00SDR2NEgwdjJoNHY0aDJ2LTRoNHYtMkg2ek02IDRWMEg0djRIMHYyaDR2NGgyVjZoNFY0SDZ6Ii8+PC9nPjwvZz48L3N2Zz4=')]">
    </div>

    <div class="relative z-10 mx-auto w-full max-w-6xl px-4 py-16 text-center sm:px-6 lg:px-8">
        {{-- Badge --}}
        <div
            class="mb-4 inline-flex items-center gap-2 rounded-full border border-emerald-500/30 bg-emerald-500/10 px-4 py-1.5">
            <flux:icon.star class="size-4 text-emerald-600 dark:text-emerald-400" />
            <span class="text-sm text-emerald-700 dark:text-emerald-300">وكيل معتمد للحج والعمرة</span>
        </div>

        {{-- Heading --}}
        <h1
            class="mx-auto max-w-7xl text-4xl font-extrabold leading-tight text-zinc-900 dark:text-white sm:text-5xl lg:text-6xl">
            رحلتك إلى <span class="text-amber-600 dark:text-amber-400">الحرمين الشريفين</span> تبدأ من هنا
        </h1>

        {{-- Subheading --}}
        <p class="mx-auto mt-6 max-w-2xl text-lg leading-relaxed text-zinc-600 dark:text-zinc-300 sm:text-xl">
            باقات حج وعمرة مميزة بأفضل الأسعار والخدمات
        </p>

        {{-- Search bar --}}
        <div class="mx-auto mt-10 max-w-7xl">
            <div
                class="rounded-xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-700 dark:bg-white/10 dark:backdrop-blur-xl dark:shadow-none sm:p-6">
                <form wire:submit="search" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                    <div class="flex-1 text-right">
                        <label class="mb-1.5 block text-xs font-medium text-zinc-600 dark:text-zinc-300">نوع
                            الرحلة</label>
                        <flux:select wire:model="searchType" class="w-full">
                            <flux:select.option value="">جميع الأنواع</flux:select.option>
                            @foreach ($this->types as $type)
                                <flux:select.option value="{{ $type['value'] }}">{{ $type['label'] }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>

                    <div class="flex-1 text-right">
                        <label class="mb-1.5 block text-xs font-medium text-zinc-600 dark:text-zinc-300">السنة</label>
                        <flux:select wire:model="searchYear" class="w-full">
                            <flux:select.option value="">جميع السنوات</flux:select.option>
                            @foreach ($this->years as $year)
                                <flux:select.option value="{{ $year['value'] }}">{{ $year['label'] }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>

                    <div class="flex-1 text-right">
                        <label class="mb-1.5 block text-xs font-medium text-zinc-600 dark:text-zinc-300">الدرجة</label>
                        <flux:select wire:model="searchGrade" class="w-full">
                            <flux:select.option value="">جميع الدرجات</flux:select.option>
                            @foreach ($this->grades as $grade)
                                <flux:select.option value="{{ $grade['value'] }}">{{ $grade['label'] }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>

                    <div>
                        <flux:button type="submit" variant="primary" icon="magnifying-glass"
                            class="w-full whitespace-nowrap sm:w-auto">
                            احجز رحلتك الآن
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>