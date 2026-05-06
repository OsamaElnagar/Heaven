<section class="bg-white py-16 dark:bg-zinc-950 sm:py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <flux:heading size="xl" class="text-zinc-900 dark:text-white">باقات مميزة</flux:heading>
            <flux:text class="mt-3 text-zinc-500 dark:text-zinc-400">اختر من أفضل باقات الحج والعمرة</flux:text>
        </div>

        @php $all = $this->packages(); @endphp

        @if ($all->isEmpty())
            <div class="mt-12 text-center">
                <div class="mx-auto flex size-20 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800">
                    <flux:icon.magnifying-glass class="size-8 text-zinc-400" />
                </div>
                <h3 class="mt-4 text-lg font-semibold text-zinc-900 dark:text-white">لا توجد باقات حالياً</h3>
                <p class="mt-1 text-zinc-500 dark:text-zinc-400">لم يتم إضافة أي باقات مميزة بعد</p>
            </div>
        @else
            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($all as $package)
                    <a href="{{ route('packages.show', $package) }}" wire:navigate
                        class="group flex flex-col overflow-hidden rounded-xl border border-zinc-200 bg-zinc-50 transition hover:border-emerald-300 hover:shadow-lg dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-emerald-700">
                        <div class="relative h-48 overflow-hidden bg-gradient-to-br from-emerald-800 to-emerald-950">
                            <div class="absolute inset-0 flex flex-col items-center justify-center text-white">
                                <flux:icon.globe-alt class="size-12 opacity-30" />
                                <span class="mt-2 text-2xl font-bold">{{ $package->name }}</span>
                            </div>
                            <div class="absolute end-3 top-3 flex gap-1.5">
                                <flux:badge variant="{{ $package->type->getColor() === 'warning' ? 'warning' : 'success' }}"
                                    size="sm">
                                    {{ $package->type->getLabel() }}
                                </flux:badge>
                                <flux:badge size="sm">
                                    {{ $package->grade->getLabel() }}
                                </flux:badge>
                            </div>
                        </div>

                        <div class="flex flex-1 flex-col p-5">
                            <div class="mb-3 flex items-center justify-between">
                                <span class="text-xl font-bold text-emerald-600 dark:text-emerald-400">
                                    {{ number_format($package->base_price, 0) }} egp
                                </span>
                                <span class="text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ $package->duration_nights }} ليلة
                                </span>
                            </div>

                            <div
                                class="mt-auto flex items-center justify-between border-t border-zinc-200 pt-3 dark:border-zinc-800">
                                <span class="flex items-center gap-1.5 text-sm text-zinc-500 dark:text-zinc-400">
                                    <flux:icon.calendar-days class="size-4" />
                                    {{ $package->departure_date?->format('Y/m/d') }}
                                </span>
                                <span
                                    class="text-sm font-medium {{ ($package->total_seats - $package->reserved_seats) > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500 dark:text-red-400' }}">
                                    {{ $package->total_seats - $package->reserved_seats }} مقعد متاح
                                </span>
                            </div>

                            <div
                                class="mt-3 flex items-center justify-between border-t border-zinc-200 pt-3 dark:border-zinc-800">
                                <span class="text-sm font-medium text-emerald-600 group-hover:underline dark:text-emerald-400">
                                    عرض التفاصيل
                                </span>
                                <flux:icon.arrow-left class="size-4 text-emerald-600 dark:text-emerald-400" />
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>