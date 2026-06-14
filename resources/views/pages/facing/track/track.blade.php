<div class="mx-auto max-w-7xl" dir="rtl">
    {{-- Header --}}
    <div class="mb-8 text-center">
        <flux:heading size="xl" class="mb-2">تتبع حجزك</flux:heading>
        <flux:text>أدخل رقم الحجز والرقم القومي لمتابعة حالة حجزك</flux:text>
    </div>

    {{-- Search Form --}}
    <flux:card class="mb-8">
        <div class="space-y-5">
            <flux:field>
                <flux:label>رقم الحجز</flux:label>
                <flux:input wire:model="reference" placeholder="HVN-XXXXXXXX" />
                <flux:error name="reference" />
            </flux:field>

            <flux:field>
                <flux:label>الرقم القومي المدخل فى بيانات الحجز</flux:label>
                <flux:input wire:model="nationalId" placeholder="أدخل الرقم القومي" />
                <flux:error name="nationalId" />
            </flux:field>

            <flux:button type="submit" variant="primary" wire:click="track" wire:loading.attr="disabled" class="w-full">
                <span wire:loading.remove wire:target="track">استعلام</span>
                <span wire:loading wire:target="track" class="flex items-center gap-2">
                    <span
                        class="animate-spin inline-block size-4 border-2 border-white/30 border-t-white rounded-full"></span>
                    جاري البحث...
                </span>
            </flux:button>
        </div>
    </flux:card>

    {{-- Error State --}}
    @if ($searched && $error)
        <div class="rounded-xl border border-red-500/30 bg-red-500/10 p-6 text-center">
            <div class="mx-auto mb-3 flex size-14 items-center justify-center rounded-full bg-red-500/20">
                <flux:icon.x-mark class="size-7 text-red-500" />
            </div>
            <flux:text class="text-red-500">{{ $error }}</flux:text>
        </div>
    @endif

    {{-- Booking Details --}}
    @if ($searched && $booking)
        <div class="space-y-6">
            {{-- Reference & Status --}}
            <flux:card>
                <div class="flex flex-col items-center gap-4 text-center sm:flex-row sm:justify-between sm:text-right">
                    <div>
                        <flux:text class="mb-1">رقم الحجز المرجعي</flux:text>
                        <flux:heading size="xl">{{ $booking->reference }}</flux:heading>
                    </div>
                    <flux:badge variant="solid" color="{{ match ($booking->status->value) {
            'pending' => 'yellow',
            'confirmed' => 'green',
            'cancelled' => 'red',
            'completed' => 'blue',
            'refunded' => 'zinc',
        } }}" size="lg" class="text-base px-4 py-2">
                        {{ $booking->status->getLabel() }}
                    </flux:badge>
                </div>
            </flux:card>

            {{-- Package Info --}}
            <flux:card>
                <flux:heading size="base" class="mb-4">معلومات الباقة</flux:heading>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <flux:text class="mb-1">اسم الباقة</flux:text>
                        <flux:heading level="3">{{ $booking->package->name }}</flux:heading>
                    </div>
                    <div>
                        <flux:text class="mb-1">النوع</flux:text>
                        <flux:badge variant="solid" color="zinc" size="sm">{{ $booking->package->type?->name_ar ?? '—' }}
                        </flux:badge>
                    </div>
                    <div>
                        <flux:text class="mb-1">الدرجة</flux:text>
                        <flux:badge variant="solid" color="zinc" size="sm">{{ $booking->package->grade->getLabel() }}
                        </flux:badge>
                    </div>
                    <div>
                        <flux:text class="mb-1">تاريخ المغادرة</flux:text>
                        <flux:heading level="3">{{ $booking->package->departure_date->format('Y/m/d') }}</flux:heading>
                    </div>
                    <div>
                        <flux:text class="mb-1">المدة</flux:text>
                        <flux:heading level="3">{{ $booking->package->duration_nights }} ليلة</flux:heading>
                    </div>
                    <div>
                        <flux:text class="mb-1">نوع الغرفة</flux:text>
                        <flux:heading level="3">{{ $booking->room_type?->getLabel() }}</flux:heading>
                    </div>
                </div>
            </flux:card>

            {{-- Payment Summary --}}
            <flux:card>
                <flux:heading size="base" class="mb-4">ملخص الدفع</flux:heading>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <flux:text>إجمالي السعر</flux:text>
                        <flux:heading level="3">{{ number_format($booking->net_price) }} egp</flux:heading>
                    </div>
                    <flux:separator />
                    <div class="flex items-center justify-between">
                        <flux:text>المدفوع</flux:text>
                        <flux:heading level="3">{{ number_format($booking->paid_amount) }} egp</flux:heading>
                    </div>
                    <flux:separator />
                    <div class="flex items-center justify-between">
                        <flux:text>المتبقي</flux:text>
                        <span @class([
                            'font-bold',
                            'text-red-600 dark:text-red-400' => $this->remaining > 0,
                            'text-green-600 dark:text-green-400' => $this->remaining <= 0,
                        ])>
                            {{ number_format($this->remaining) }} egp
                        </span>
                    </div>
                </div>
            </flux:card>

            {{-- Visa Status --}}
            <flux:card>
                <flux:heading size="base" class="mb-4">حالة التأشيرة</flux:heading>
                @if ($booking->visa)
                        <flux:badge variant="solid" color="{{ match ($booking->visa->status->value) {
                        'not_applied' => 'zinc',
                        'applied' => 'yellow',
                        'approved' => 'green',
                        'rejected' => 'red',
                        'expired' => 'zinc',
                    } }}" size="lg" class="text-base px-4 py-2">
                            {{ $booking->visa->status->getLabel() }}
                        </flux:badge>
                @else
                    <flux:text>{{ __('لم يتم التقديم بعد') }}</flux:text>
                @endif
            </flux:card>
        </div>
    @endif
</div>
