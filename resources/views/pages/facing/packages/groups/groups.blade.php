<div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8" dir="rtl">
    <div class="mb-8 text-center">
        <flux:heading size="xl">رحلات المجموعات</flux:heading>
        <p class="mt-2 text-zinc-600 dark:text-zinc-400">باقات خاصة للمجموعات والعائلات الكبيرة</p>
    </div>

    @php $packages = $this->packages(); @endphp

    @if ($packages->isEmpty())
        <div class="rounded-xl border border-dashed border-zinc-300 py-16 text-center dark:border-zinc-600">
            <flux:icon.magnifying-glass class="mx-auto size-12 text-zinc-300" />
            <p class="mt-4 text-lg text-zinc-500 dark:text-zinc-400">لا توجد رحلات مجموعات متاحة حالياً</p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($packages as $package)
                <a href="{{ route('packages.show', $package) }}" wire:navigate
                    class="group block rounded-xl border border-zinc-200 bg-white p-5 shadow-sm
                           transition hover:border-emerald-300 hover:shadow-md
                           dark:border-zinc-700 dark:bg-zinc-800 dark:hover:border-emerald-600">
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-zinc-900 group-hover:text-emerald-700 dark:text-white">
                            {{ $package->name }}
                        </h3>
                    </div>

                    <div class="mb-4 flex flex-wrap gap-2">
                        <flux:badge variant="solid"
                            color="{{ $package->type === App\Enums\PackageType::HAJJ ? 'warning' : 'success' }}">
                            {{ $package->type->getLabel() }}
                        </flux:badge>
                        <flux:badge variant="subtle" color="{{ match ($package->grade) {
                            App\Enums\PackageGrade::ECONOMY => 'zinc',
                            App\Enums\PackageGrade::STANDARD => 'blue',
                            App\Enums\PackageGrade::VIP => 'amber',
                            App\Enums\PackageGrade::VVIP => 'red',
                        } }}">
                            {{ $package->grade->getLabel() }}
                        </flux:badge>
                    </div>

                    <div class="space-y-2 text-sm text-zinc-600 dark:text-zinc-400">
                        <div class="flex items-center gap-2">
                            <flux:icon.currency-dollar class="size-4 text-emerald-600" />
                            <span class="text-lg font-bold text-emerald-700 dark:text-emerald-300">
                                {{ number_format($package->base_price) }} egp
                            </span>
                        </div>

                        <div class="flex items-center gap-2">
                            <flux:icon.clock class="size-4 text-zinc-400" />
                            <span>{{ $package->duration_nights }} ليلة</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <flux:icon.calendar-days class="size-4 text-zinc-400" />
                            <span>{{ $package->departure_date->translatedFormat('j F Y') }}</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <flux:icon.user-group class="size-4 text-zinc-400" />
                            @php $remaining = $package->total_seats - $package->reserved_seats; @endphp
                            @if ($remaining > 0)
                                <span class="text-emerald-600 dark:text-emerald-400">{{ $remaining }} مقعد متاح</span>
                            @else
                                <span class="text-red-500 dark:text-red-400">نفذت المقاعد</span>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4">
                        <span
                            class="inline-flex items-center gap-1 text-sm font-medium text-emerald-600 transition group-hover:gap-2 dark:text-emerald-400">
                            عرض التفاصيل
                            <flux:icon.arrow-right class="size-4" />
                        </span>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $packages->links() }}
        </div>
    @endif
</div>
