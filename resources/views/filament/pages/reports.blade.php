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
        <x-filament::section>
            <x-slot name="heading">حالة التأشيرات - آخر الرحلات</x-slot>
            <div class="space-y-4">
                @foreach($visaReport as $vr)
                <div class="border rounded p-3">
                    <h4 class="font-bold mb-2">{{ $vr['trip'] }}</h4>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-2 text-center text-sm">
                        @foreach($vr['counts'] as $status => $count)
                        <div class="bg-gray-50 p-2 rounded">
                            <span class="text-lg font-bold">{{ $count }}</span>
                            <span class="block text-xs text-gray-500">{{ $status }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
