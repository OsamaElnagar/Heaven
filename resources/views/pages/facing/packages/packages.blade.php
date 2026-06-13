<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8" dir="rtl">
    <flux:heading size="xl" class="mb-8 text-center">
        باقات الحج والعمرة
    </flux:heading>

    {{-- Filter Bar --}}
    <div
        class="mb-8 rounded-xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-700 dark:bg-zinc-800 dark:shadow-none sm:p-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <flux:label class="mb-1 block text-sm font-medium">نوع الرحلة</flux:label>
                <flux:select wire:model.live="type" class="w-full">
                    <option value="">الكل</option>
                    @foreach ($this->types() as $t)
                        <option value="{{ $t['value'] }}">{{ $t['label'] }}</option>
                    @endforeach
                </flux:select>
            </div>

            <div>
                <flux:label class="mb-1 block text-sm font-medium">الدرجة</flux:label>
                <flux:select wire:model.live="grade" class="w-full">
                    <option value="">الكل</option>
                    @foreach ($this->grades() as $g)
                        <option value="{{ $g['value'] }}">{{ $g['label'] }}</option>
                    @endforeach
                </flux:select>
            </div>

            <div>
                <flux:label class="mb-1 block text-sm font-medium">السنة</flux:label>
                <flux:select wire:model.live="year" class="w-full">
                    <option value="">الكل</option>
                    @foreach ($this->years() as $y)
                        <option value="{{ $y['value'] }}">{{ $y['label'] }}</option>
                    @endforeach
                </flux:select>
            </div>

            <div class="flex items-end">
                <flux:button wire:click="resetFilters" variant="outline" class="w-full">
                    إعادة ضبط الفلاتر
                </flux:button>
            </div>

            <div>
                <flux:label class="mb-1 block text-sm font-medium">أقل سعر (egp)</flux:label>
                <flux:input type="number" wire:model.live="minPrice" min="0" placeholder="0" class="w-full" />
            </div>

            <div>
                <flux:label class="mb-1 block text-sm font-medium">أعلى سعر (egp)</flux:label>
                <flux:input type="number" wire:model.live="maxPrice" min="0" placeholder="1000000" class="w-full" />
            </div>

            <div>
                <flux:label class="mb-1 block text-sm font-medium">أقل مدة (ليلة)</flux:label>
                <flux:input type="number" wire:model.live="minDuration" min="0" placeholder="0" class="w-full" />
            </div>

            <div>
                <flux:label class="mb-1 block text-sm font-medium">أعلى مدة (ليلة)</flux:label>
                <flux:input type="number" wire:model.live="maxDuration" min="0" placeholder="60" class="w-full" />
            </div>
        </div>
    </div>

    {{-- Results --}}
    @php $packages = $this->packages(); @endphp

    @if ($packages->isEmpty())
        <div class="rounded-xl border border-dashed border-zinc-300 py-16 text-center dark:border-zinc-600">
            <flux:icon.magnifying-glass class="mx-auto size-12 text-zinc-300" />
            <p class="mt-4 text-lg text-zinc-500 dark:text-zinc-400">لا توجد باقات متاحة حالياً</p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($packages as $package)
                <a href="{{ route('packages.show', $package) }}" wire:navigate
                    class="group block rounded-xl border border-zinc-200 bg-white p-5 shadow-sm transition hover:border-emerald-300 hover:shadow-md dark:border-zinc-700 dark:bg-zinc-800 dark:shadow-none">
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-zinc-900 group-hover:text-emerald-700 dark:text-white">
                            {{ $package->name }}
                        </h3>
                    </div>

                    <div class="mb-4 flex flex-wrap gap-2">
                        <flux:badge variant="solid"
                            color="{{ $package->type?->color ?? 'gray' }}">
                            {{ $package->type?->name_ar ?? '—' }}
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