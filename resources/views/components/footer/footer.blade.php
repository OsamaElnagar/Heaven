<footer
    class="bg-white dark:bg-[#1c1916] border-t border-stone-200 dark:border-stone-800
           bg-none font-tajawal"
    style="--gold: #C9A84C;"
    dir="rtl"
>
    {{-- Top gold accent line --}}
    <div class="h-[3px] bg-linear-to-r from-transparent via-[#C9A84C] to-transparent"></div>

    {{-- Main grid --}}
    <div class="max-w-7xl mx-auto px-6 py-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">

        {{-- Brand --}}
        <div class="sm:col-span-2 lg:col-span-1">
            <div class="flex items-center gap-3 mb-4">
                <div class="flex aspect-square size-10 items-center justify-center rounded-md bg-accent-content text-accent-foreground"><x-app-logo-icon class="size-5 fill-current text-white dark:text-black" /></div>
                <div class="flex flex-col leading-tight">
                    <span class="text-lg font-bold text-stone-900 dark:text-stone-100">HEAVEN</span>
                    <span class="text-[0.65rem] tracking-widest text-[#C9A84C]">للحج والعمرة</span>
                </div>
            </div>
            <p class="text-sm text-stone-500 dark:text-stone-400 leading-relaxed mb-5 max-w-xs">
                رحلات حج وعمرة بمستوى خمس نجوم — نُيسّر لكم أداء شعائركم بكل راحة وطمأنينة منذ عام ٢٠٠٥.
            </p>
            <div class="flex flex-wrap gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded border border-stone-200
                             dark:border-stone-700 bg-stone-50 dark:bg-stone-800 text-xs text-stone-500
                             dark:text-stone-400">
                    <x-heroicon-o-check-circle class="w-3 h-3 text-[#C9A84C]" />
                    مرخص من وزارة السياحة
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded border border-stone-200
                             dark:border-stone-700 bg-stone-50 dark:bg-stone-800 text-xs text-stone-500
                             dark:text-stone-400">
                    <x-heroicon-o-star class="w-3 h-3 text-[#C9A84C]" />
                    عضو IATA
                </span>
            </div>
        </div>

        {{-- Packages --}}
        <div>
            <h4 class="text-[0.7rem] font-bold tracking-widest text-[#C9A84C] uppercase mb-4">الباقات</h4>
            <ul class="space-y-2.5">
                @foreach([
                    ['label' => 'باقات الحج',       'url' => route('packages.index', ['type' => 'hajj'])],
                    ['label' => 'باقات العمرة',      'url' => route('packages.index', ['type' => 'umrah'])],
                    ['label' => 'العروض المميزة',    'url' => route('packages.featured')],
                    ['label' => 'باقات VIP',         'url' => route('packages.vip')],
                    ['label' => 'رحلات المجموعات',   'url' => route('packages.groups')],
                ] as $link)
                    <li>
                        <a href="{{ $link['url'] }}"
                           class="text-sm text-stone-500 dark:text-stone-400 hover:text-[#C9A84C]
                                  dark:hover:text-[#E2C47A] hover:pr-1.5 transition-all duration-200
                                  inline-block">
                            {{ $link['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Company --}}
        <div>
            <h4 class="text-[0.7rem] font-bold tracking-widest text-[#C9A84C] uppercase mb-4">الشركة</h4>
            <ul class="space-y-2.5">
                @foreach([
                    ['label' => 'من نحن',         'route' => 'about'],
                    ['label' => 'تتبع حجزك',       'route' => 'track'],
                    ['label' => 'الأسئلة الشائعة', 'route' => 'faq'],
                    ['label' => 'المعرض',          'route' => 'gallery'],
                    ['label' => 'تواصل معنا',      'route' => 'contact'],
                ] as $link)
                    <li>
                        <a href="{{ route($link['route']) }}"
                           class="text-sm text-stone-500 dark:text-stone-400 hover:text-[#C9A84C]
                                  dark:hover:text-[#E2C47A] hover:pr-1.5 transition-all duration-200
                                  inline-block">
                            {{ $link['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Contact --}}
        <div>
            <h4 class="text-[0.7rem] font-bold tracking-widest text-[#C9A84C] uppercase mb-4">تواصل معنا</h4>
            <div class="space-y-3">
                @foreach([
                    ['icon' => 'heroicon-o-phone',         'label' => 'الهاتف',           'value' => '+20 10 0000 0000'],
                    ['icon' => 'heroicon-o-envelope',      'label' => 'البريد الإلكتروني','value' => 'info@heaven-travel.com'],
                    ['icon' => 'heroicon-o-map-pin',       'label' => 'العنوان',          'value' => 'القاهرة، مصر الجديدة'],
                ] as $item)
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-md flex items-center justify-center shrink-0
                                    bg-stone-50 dark:bg-stone-800 border border-stone-200 dark:border-stone-700">
                            <x-dynamic-component :component="$item['icon']"
                                                 class="w-3.5 h-3.5 text-[#C9A84C]" />
                        </div>
                        <div>
                            <p class="text-[0.65rem] text-stone-400 dark:text-stone-500">{{ $item['label'] }}</p>
                            <p class="text-sm text-stone-600 dark:text-stone-300 font-medium">{{ $item['value'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Social --}}
            <div class="flex gap-2 mt-5">
                @foreach([
                    ['label' => 'واتساب',  'href' => '#', 'icon' => 'whatsapp'],
                    ['label' => 'فيسبوك',  'href' => '#', 'icon' => 'facebook'],
                    ['label' => 'انستغرام','href' => '#', 'icon' => 'instagram'],
                ] as $s)
                    <a href="{{ $s['href'] }}"
                       class="w-8 h-8 rounded-md flex items-center justify-center
                              border border-stone-200 dark:border-stone-700
                              bg-stone-50 dark:bg-stone-800
                              hover:bg-[#C9A84C] hover:border-[#C9A84C]
                              text-stone-400 hover:text-white
                              transition-all duration-200">
                        {{-- swap with your preferred SVG or Blade icon pack --}}
                        <span class="text-xs">{{ mb_substr($s['label'], 0, 1) }}</span>
                    </a>
                @endforeach
            </div>
        </div>

    </div>

    {{-- Bottom bar --}}
    <div class="border-t border-stone-200 dark:border-stone-800">
        <div class="max-w-7xl mx-auto px-6 py-4 flex flex-wrap items-center justify-between gap-3">
            <p class="text-xs text-stone-400 dark:text-stone-500">
                © {{ date('Y') }} <span class="text-[#C9A84C]">HEAVEN للحج والعمرة</span>. جميع الحقوق محفوظة.
            </p>
            <div class="flex gap-5">
                @foreach([
                    ['label' => 'سياسة الخصوصية', 'url' => route('privacy')],
                    ['label' => 'الشروط والأحكام', 'url' => route('terms')],
                    ['label' => 'سياسة الإلغاء',  'url' => route('cancellation')],
                ] as $link)
                    <a href="{{ $link['url'] }}"
                       class="text-xs text-stone-400 dark:text-stone-500
                              hover:text-[#C9A84C] dark:hover:text-[#E2C47A] transition-colors">
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</footer>