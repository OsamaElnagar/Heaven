<footer
    class="bg-white dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-700
           bg-none font-tajawal"
    dir="rtl">
    {{-- Top accent line --}}
    <div class="h-[3px] bg-linear-to-r from-transparent via-emerald-400 to-transparent"></div>

    {{-- Main grid --}}
    <div class="max-w-7xl mx-auto px-6 py-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">

        {{-- Brand --}}
        <div class="sm:col-span-2 lg:col-span-1">
            <div class="flex items-center gap-3 mb-4">
                <div
                    class="flex aspect-square size-10 items-center justify-center rounded-md bg-accent-content text-accent-foreground">
                    <x-app-logo-icon class="size-5 fill-current text-white dark:text-black" />
                </div>
                <div class="flex flex-col leading-tight">
                    <span class="text-lg font-bold text-zinc-900 dark:text-white">{{ config('app.name') }}</span>
                    <span class="text-[0.65rem] tracking-widest text-emerald-600 dark:text-emerald-400">للحج والعمرة</span>
                </div>
            </div>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed mb-5 max-w-xs">
                رحلات حج وعمرة بمستوى خمس نجوم — نُيسّر لكم أداء شعائركم بكل راحة وطمأنينة منذ عام ٢٠٠٥.
            </p>
            <div class="flex flex-wrap gap-2">
                <span
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded border border-zinc-200
                             dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-xs text-zinc-500
                             dark:text-zinc-400">
                    <x-heroicon-o-check-circle class="w-3 h-3 text-emerald-600 dark:text-emerald-400" />
                    مرخص من وزارة السياحة
                </span>
                <span
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded border border-zinc-200
                             dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-xs text-zinc-500
                             dark:text-zinc-400">
                    <x-heroicon-o-star class="w-3 h-3 text-emerald-600 dark:text-emerald-400" />
                    عضو IATA
                </span>
            </div>
        </div>

        {{-- Packages --}}
        <div>
            <h4 class="text-[0.7rem] font-bold tracking-widest text-emerald-600 dark:text-emerald-400 uppercase mb-4">الباقات</h4>
            <ul class="space-y-2.5">
                @foreach ([['label' => 'باقات الحج', 'url' => route('packages.index', ['type' => 'hajj'])], ['label' => 'باقات العمرة', 'url' => route('packages.index', ['type' => 'umrah'])], ['label' => 'العروض المميزة', 'url' => route('packages.featured')], ['label' => 'باقات VIP', 'url' => route('packages.vip')], ['label' => 'رحلات المجموعات', 'url' => route('packages.groups')]] as $link)
                    <li>
                        <a href="{{ $link['url'] }}"
                            class="text-sm text-zinc-500 dark:text-zinc-400 hover:text-emerald-600
                                  dark:hover:text-emerald-400 hover:pr-1.5 transition-all duration-200
                                  inline-block">
                            {{ $link['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Company --}}
        <div>
            <h4 class="text-[0.7rem] font-bold tracking-widest text-emerald-600 dark:text-emerald-400 uppercase mb-4">الشركة</h4>
            <ul class="space-y-2.5">
                @foreach ([['label' => 'من نحن', 'route' => 'about'], ['label' => 'تتبع حجزك', 'route' => 'track'], ['label' => 'الأسئلة الشائعة', 'route' => 'faq'], ['label' => 'الأخبار', 'route' => 'news.index'], ['label' => 'المعرض', 'route' => 'gallery'], ['label' => 'تواصل معنا', 'route' => 'contact']] as $link)
                    <li>
                        <a href="{{ route($link['route']) }}"
                            class="text-sm text-zinc-500 dark:text-zinc-400 hover:text-emerald-600
                                  dark:hover:text-emerald-400 hover:pr-1.5 transition-all duration-200
                                  inline-block">
                            {{ $link['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Contact --}}
        <div>
            <h4 class="text-[0.7rem] font-bold tracking-widest text-emerald-600 dark:text-emerald-400 uppercase mb-4">تواصل معنا</h4>
            <div class="space-y-3">
                @foreach ([['icon' => 'heroicon-o-phone', 'label' => 'الهاتف', 'value' => '+20 10 0000 0000'], ['icon' => 'heroicon-o-envelope', 'label' => 'البريد الإلكتروني', 'value' => config('app.email')], ['icon' => 'heroicon-o-map-pin', 'label' => 'العنوان', 'value' => 'القاهرة، مصر الجديدة']] as $item)
                    <div class="flex items-start gap-3">
                        <div
                            class="w-8 h-8 rounded-md flex items-center justify-center shrink-0
                                    bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700">
                            <x-dynamic-component :component="$item['icon']" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <div>
                            <p class="text-[0.65rem] text-zinc-400 dark:text-zinc-500">{{ $item['label'] }}</p>
                            <p class="text-sm text-zinc-600 dark:text-zinc-300 font-medium">{{ $item['value'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Social --}}
            <div class="flex gap-2 mt-5">
                @foreach ([['label' => 'واتساب', 'href' => '#', 'icon' => 'whatsapp'], ['label' => 'فيسبوك', 'href' => '#', 'icon' => 'facebook'], ['label' => 'انستغرام', 'href' => '#', 'icon' => 'instagram']] as $s)
                    <a href="{{ $s['href'] }}"
                        class="w-8 h-8 rounded-md flex items-center justify-center
                              border border-zinc-200 dark:border-zinc-700
                              bg-zinc-50 dark:bg-zinc-800
                              hover:bg-emerald-600 hover:border-emerald-600
                              text-zinc-400 hover:text-white
                              transition-all duration-200">
                        {{-- swap with your preferred SVG or Blade icon pack --}}
                        <span class="text-xs">{{ mb_substr($s['label'], 0, 1) }}</span>
                    </a>
                @endforeach
            </div>
        </div>

    </div>

    {{-- Bottom bar --}}
    <div class="border-t border-zinc-200 dark:border-zinc-700">
        <div class="max-w-7xl mx-auto px-6 py-4 flex flex-wrap items-center justify-between gap-3">
            <p class="text-xs text-zinc-400 dark:text-zinc-500">
                © {{ date('Y') }} <span class="text-emerald-600 dark:text-emerald-400">{{ config('app.name') }} للحج والعمرة</span>. جميع
                الحقوق محفوظة.
            </p>
            <div class="flex gap-5">
                @foreach ([['label' => 'سياسة الخصوصية', 'url' => route('privacy')], ['label' => 'الشروط والأحكام', 'url' => route('terms')], ['label' => 'سياسة الإلغاء', 'url' => route('cancellation')]] as $link)
                    <a href="{{ $link['url'] }}"
                        class="text-xs text-zinc-400 dark:text-zinc-500
                              hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</footer>
