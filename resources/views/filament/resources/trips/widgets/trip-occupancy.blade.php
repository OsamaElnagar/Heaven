<x-filament::widget>
    <x-filament::section>
        <x-slot name="heading">إشغال الفنادق</x-slot>
        <div class="space-y-4">
            @forelse($occupancy as $hotel)
                @php
                    $fillRate = $hotel['fill_rate'];
                    $barColor = match (true) {
                        $fillRate >= 90 => 'bg-danger-500',
                        $fillRate >= 70 => 'bg-warning-500',
                        default => 'bg-success-500',
                    };
                @endphp
                <div>
                    <div class="flex justify-between mb-2">
                        <span class="font-medium">{{ $hotel['hotel'] }}
                            @if($hotel['city'] !== 'غير محدد')
                                <span class="text-sm text-gray-500">- {{ $hotel['city'] }}</span>
                            @endif
                        </span>
                        <span class="text-sm text-gray-600">{{ $hotel['occupied'] }}/{{ $hotel['capacity'] }} ({{ $fillRate }}%)</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="h-2.5 rounded-full {{ $barColor }}" style="width: {{ $fillRate }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-center py-4">لا توجد غرف مضافة لهذه الرحلة</p>
            @endforelse
        </div>
    </x-filament::section>
</x-filament::widget>
