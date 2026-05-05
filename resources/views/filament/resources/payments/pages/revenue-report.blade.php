<x-filament-panels::page>
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-filament::section>
                <x-slot name="heading">إجمالي الحجوزات - {{ $year }}</x-slot>
                <p class="text-3xl font-bold">{{ $report['total_bookings'] }}</p>
            </x-filament::section>
            <x-filament::section>
                <x-slot name="heading">إجمالي المحصل</x-slot>
                <p class="text-3xl font-bold text-success-500">{{ number_format($report['total_collected'], 0) }} ج.م</p>
            </x-filament::section>
            <x-filament::section>
                <x-slot name="heading">المستحق</x-slot>
                <p class="text-3xl font-bold text-danger-500">{{ number_format($report['total_outstanding'], 0) }} ج.م</p>
            </x-filament::section>
        </div>
        {{ $this->table }}
    </div>
</x-filament-panels::page>
