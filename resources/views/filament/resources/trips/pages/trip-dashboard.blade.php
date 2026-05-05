<x-filament-panels::page>
    <div class="space-y-6">
        {{ $this->infolist }}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($visas as $status => $count)
                <x-filament::section>
                    <x-slot name="heading">{{ $status }}</x-slot>
                    <span class="text-2xl font-bold">{{ $count }}</span>
                </x-filament::section>
            @endforeach
        </div>
        <x-filament::section>
            <x-slot name="heading">إشغال الفنادق</x-slot>
            <div class="space-y-4">
                @foreach($occupancy as $hotel)
                    <div class="border rounded p-3">
                        <div class="flex justify-between mb-2">
                            <span class="font-bold">{{ $hotel['hotel'] }} - {{ $hotel['city'] }}</span>
                            <span>{{ $hotel['occupied'] }}/{{ $hotel['capacity'] }} ({{ $hotel['fill_rate'] }}%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-success-500 h-2 rounded-full" style="width: {{ $hotel['fill_rate'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>