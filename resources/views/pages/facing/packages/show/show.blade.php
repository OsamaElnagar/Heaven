<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8" dir="rtl">
    {{-- Breadcrumb --}}
    <nav class="mb-6 flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
        <a href="{{ route('home') }}" wire:navigate class="hover:text-zinc-700 dark:hover:text-zinc-300">الرئيسية</a>
        <flux:icon.arrow-right class="size-3 rotate-180" />
        <a href="{{ route('packages.index') }}" wire:navigate
            class="hover:text-zinc-700 dark:hover:text-zinc-300">الباقات</a>
        <flux:icon.arrow-right class="size-3 rotate-180" />
        <span class="text-zinc-900 dark:text-white">{{ $package->name }}</span>
    </nav>

    {{-- Header --}}
    <div class="mb-8">
        <div class="mb-3 flex flex-wrap items-center gap-3">
            <flux:badge variant="solid"
                color="{{ $package->type === App\Enums\PackageType::HAJJ ? 'warning' : 'success' }}" size="lg">
                {{ $package->type->getLabel() }}
            </flux:badge>
            <flux:badge variant="subtle" color="{{ match ($package->grade) {
    App\Enums\PackageGrade::ECONOMY => 'zinc',
    App\Enums\PackageGrade::STANDARD => 'blue',
    App\Enums\PackageGrade::VIP => 'amber',
    App\Enums\PackageGrade::VVIP => 'red',
} }}" size="lg">
                {{ $package->grade->getLabel() }}
            </flux:badge>
        </div>

        <flux:heading size="xl">
            {{ $package->name }}
        </flux:heading>

        <div class="mt-4 flex items-center gap-3">
            <flux:icon.currency-dollar class="size-6 text-emerald-600" />
            <span class="text-3xl font-extrabold text-emerald-700 dark:text-emerald-300">
                {{ number_format($package->base_price) }} egp
            </span>
            <span class="text-sm text-zinc-500 dark:text-zinc-400">للفرد في الغرفة الثلاثية</span>
        </div>
    </div>

    {{-- Trip Details --}}
    <section
        class="mb-8 rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-800 dark:shadow-none">
        <flux:heading size="lg" class="mb-4">تفاصيل الرحلة</flux:heading>

        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
            <div class="rounded-lg bg-zinc-50 p-3 text-center dark:bg-zinc-900">
                <flux:icon.calendar-days class="mx-auto size-5 text-emerald-600" />
                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">تاريخ المغادرة</p>
                <p class="text-sm font-semibold text-zinc-900 dark:text-white">
                    {{ $package->departure_date->translatedFormat('j F Y') }}
                </p>
            </div>

            <div class="rounded-lg bg-zinc-50 p-3 text-center dark:bg-zinc-900">
                <flux:icon.calendar-days class="mx-auto size-5 text-emerald-600" />
                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">تاريخ العودة</p>
                <p class="text-sm font-semibold text-zinc-900 dark:text-white">
                    {{ $package->return_date->translatedFormat('j F Y') }}
                </p>
            </div>

            <div class="rounded-lg bg-zinc-50 p-3 text-center dark:bg-zinc-900">
                <flux:icon.clock class="mx-auto size-5 text-emerald-600" />
                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">المدة</p>
                <p class="text-sm font-semibold text-zinc-900 dark:text-white">
                    {{ $package->duration_nights }} ليلة
                </p>
            </div>

            <div class="rounded-lg bg-zinc-50 p-3 text-center dark:bg-zinc-900">
                <flux:icon.user-group class="mx-auto size-5 text-emerald-600" />
                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">المقاعد المتاحة</p>
                <p
                    class="text-sm font-semibold {{ $this->availableSeats() > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500 dark:text-red-400' }}">
                    {{ $this->availableSeats() > 0 ? $this->availableSeats() : 'نفذت' }}
                </p>
            </div>

            <div class="rounded-lg bg-zinc-50 p-3 text-center dark:bg-zinc-900">
                <flux:icon.star class="mx-auto size-5 text-emerald-600" />
                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">موسم</p>
                <p class="text-sm font-semibold text-zinc-900 dark:text-white">
                    {{ $package->season_year }}
                </p>
            </div>
        </div>
    </section>

    {{-- Hotels --}}
    @if ($package->hotels->isNotEmpty())
        <section
            class="mb-8 rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-800 dark:shadow-none">
            <flux:heading size="lg" class="mb-4">الفنادق</flux:heading>

            <div class="space-y-4">
                @foreach ($package->hotels as $hotel)
                    <div class="rounded-lg border border-zinc-100 bg-zinc-50/50 p-4 dark:border-zinc-800 dark:bg-zinc-800/50">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <div class="flex items-center gap-2">
                                    <flux:icon.building-office class="size-5 text-zinc-500" />
                                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">{{ $hotel->name }}</h3>
                                </div>
                                <div
                                    class="mt-1 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-zinc-600 dark:text-zinc-400">
                                    <span class="flex items-center gap-1">
                                        <flux:icon.map-pin class="size-3.5 text-zinc-400" />
                                        {{ $hotel->city }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <flux:icon.star
                                                class="size-3.5 {{ $i <= $hotel->stars ? 'text-amber-400' : 'text-zinc-200 dark:text-zinc-600' }}" />
                                        @endfor
                                    </span>
                                    <span>
                                        يبعد عن الحرم {{ $hotel->distance_to_haram }} متر
                                    </span>
                                </div>
                            </div>

                            <div class="shrink-0 rounded-lg bg-emerald-50 px-4 py-2 text-center dark:bg-emerald-950">
                                <p class="text-xs text-emerald-700 dark:text-emerald-300">عدد الليالي</p>
                                <p class="text-lg font-bold text-emerald-700 dark:text-emerald-300">{{ $hotel->pivot->nights }}
                                    ليلة</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Includes / Excludes --}}
    <section
        class="mb-8 rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-800 dark:shadow-none">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            {{-- Includes --}}
            <div>
                <flux:heading size="lg" class="mb-3 flex items-center gap-2 text-emerald-700">
                    <flux:icon.check class="size-5" />
                    الخدمات المشمولة
                </flux:heading>
                @if ($package->includes)
                    <ul class="space-y-2">
                        @foreach (explode("\n", $package->includes) as $line)
                            @php $line = trim($line); @endphp
                            @if ($line !== '')
                                <li class="flex items-start gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                                    <flux:icon.check class="mt-0.5 size-4 shrink-0 text-emerald-500" />
                                    <span>{{ $line }}</span>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-zinc-400">لا يوجد تفاصيل إضافية</p>
                @endif
            </div>

            {{-- Excludes --}}
            <div>
                <flux:heading size="lg" class="mb-3 flex items-center gap-2 text-red-600">
                    <flux:icon.x-mark class="size-5" />
                    الخدمات غير المشمولة
                </flux:heading>
                @if ($package->excludes)
                    <ul class="space-y-2">
                        @foreach (explode("\n", $package->excludes) as $line)
                            @php $line = trim($line); @endphp
                            @if ($line !== '')
                                <li class="flex items-start gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                                    <flux:icon.x-mark class="mt-0.5 size-4 shrink-0 text-red-400" />
                                    <span>{{ $line }}</span>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-emerald-600 dark:text-emerald-400">جميع الخدمات مشمولة</p>
                @endif
            </div>
        </div>
    </section>

    {{-- Room Type Pricing --}}
    <section
        class="mb-8 rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-800 dark:shadow-none">
        <flux:heading size="lg" class="mb-4">أسعار الغرف</flux:heading>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-zinc-700">
                        <th class="px-4 py-3 font-semibold text-zinc-900 dark:text-white">نوع الغرفة</th>
                        <th class="px-4 py-3 font-semibold text-zinc-900 dark:text-white">السعر للفرد</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                    <tr>
                        <td class="px-4 py-3 font-medium text-zinc-800 dark:text-zinc-200">فردي</td>
                        <td class="px-4 py-3 text-emerald-700 dark:text-emerald-300">
                            {{ number_format($this->singlePrice()) }} egp
                        </td>
                    </tr>
                    <tr class="bg-zinc-50/50 dark:bg-zinc-800/50">
                        <td class="px-4 py-3 font-medium text-zinc-800 dark:text-zinc-200">ثنائي</td>
                        <td class="px-4 py-3 text-emerald-700 dark:text-emerald-300">
                            {{ number_format($this->doublePrice()) }} egp
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-medium text-zinc-800 dark:text-zinc-200">ثلاثي</td>
                        <td class="px-4 py-3 text-emerald-700 dark:text-emerald-300">
                            {{ number_format($this->triplePrice()) }} egp
                        </td>
                    </tr>
                    <tr class="bg-zinc-50/50 dark:bg-zinc-800/50">
                        <td class="px-4 py-3 font-medium text-zinc-800 dark:text-zinc-200">رباعي</td>
                        <td class="px-4 py-3 text-emerald-700 dark:text-emerald-300">
                            {{ number_format($this->quadPrice()) }} egp
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    {{-- CTA --}}
    <section class="rounded-xl bg-emerald-50 p-6 text-center shadow-sm dark:bg-emerald-950 sm:p-8">
        @php $seats = $this->availableSeats(); @endphp

        @if ($seats > 0)
            <p class="mb-2 text-sm text-emerald-700 dark:text-emerald-300">
                {{ $seats }} مقعد متاح من أصل {{ $package->total_seats }}
            </p>

            <flux:button variant="primary" href="{{ route('book', $package) }}" wire:navigate icon="arrow-right"
                class="w-full font-bold sm:w-auto">
                احجز الآن
            </flux:button>
        @else
            <p class="mb-2 text-lg font-semibold text-red-600 dark:text-red-400">نفذت المقاعد</p>
            <p class="mb-4 text-sm text-zinc-500 dark:text-zinc-400">جميع المقاعد محجوزة بالكامل</p>

            <flux:button variant="primary" disabled class="w-full sm:w-auto">
                احجز الآن
            </flux:button>
        @endif
    </section>
</div>