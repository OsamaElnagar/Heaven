<div class="mx-auto max-w-7xl" dir="rtl">
    {{-- Breadcrumb --}}
    <nav class="mb-8 flex items-center gap-2 text-sm text-zinc-400">
        <a href="{{ route('home') }}" class="hover:text-zinc-200 transition-colors">الرئيسية</a>
        <flux:icon.chevron-left class="size-3" />
        <a href="{{ route('packages.index') }}" class="hover:text-zinc-200 transition-colors">الباقات</a>
        <flux:icon.chevron-left class="size-3" />
        <a href="{{ route('packages.show', $package) }}"
            class="hover:text-zinc-200 transition-colors">{{ $package->name }}</a>
        <flux:icon.chevron-left class="size-3" />
        <span class="text-white">احجز الآن</span>
    </nav>

    @if ($submitted)
        {{-- Success State --}}
        <div class="text-center py-16">
            <div class="mx-auto mb-6 flex size-20 items-center justify-center rounded-full bg-emerald-500/10">
                <flux:icon.check-badge class="size-10 text-emerald-400" />
            </div>

            <flux:heading size="xl" class="mb-3 text-white">تم استلام طلبك بنجاح</flux:heading>

            <div class="mx-auto mb-8 max-w-md rounded-xl border border-emerald-500/20 bg-emerald-500/5 p-6">
                <p class="mb-2 text-sm text-zinc-400">رقم الحجز المرجعي</p>
                <p class="text-3xl font-bold tracking-widest text-emerald-400">{{ $bookingReference }}</p>
            </div>

            <flux:text class="mb-10 text-zinc-400">
                سيتواصل معك فريق المبيعات خلال 24 ساعة لإتمام الحجز
            </flux:text>

            <div>
                <flux:button href="{{ route('home') }}" variant="primary" icon="arrow-right">
                    العودة للرئيسية
                </flux:button>
            </div>
        </div>
    @else
        {{-- Package Summary --}}
        <flux:card class="mb-8">
            <div class="flex flex-wrap items-center gap-3">
                <flux:heading size="lg" class="text-white">{{ $package->name }}</flux:heading>
                <flux:badge variant="solid" color="zinc">{{ $package->type->getLabel() }}</flux:badge>
                <flux:badge variant="solid" color="zinc">{{ $package->grade->getLabel() }}</flux:badge>
                <span class="text-sm text-zinc-400">
                    <flux:icon.calendar-days class="inline size-4 me-1" />
                    {{ $package->departure_date->format('Y/m/d') }}
                </span>
            </div>
        </flux:card>

        {{-- Booking Form --}}
        <flux:card>
            <flux:heading size="lg" class="mb-6 text-white">نموذج الحجز</flux:heading>

            <div class="space-y-5">
                <flux:field>
                    <flux:label>الاسم الكامل</flux:label>
                    <flux:input wire:model="name" placeholder="أدخل الاسم الثلاثي" />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>رقم الجوال</flux:label>
                    <flux:input wire:model="phone" placeholder="05xxxxxxxx" />
                    <flux:error name="phone" />
                </flux:field>

                <flux:field>
                    <flux:label>الرقم القومي</flux:label>
                    <flux:input wire:model="nationalId" placeholder="أدخل الرقم القومي" />
                    <flux:error name="nationalId" />
                </flux:field>

                <flux:field>
                    <flux:label>البريد الإلكتروني <span class="text-zinc-500 text-xs">(اختياري)</span></flux:label>
                    <flux:input wire:model="email" type="email" placeholder="example@domain.com" />
                    <flux:error name="email" />
                </flux:field>

                <flux:field>
                    <flux:label>عدد المسافرين</flux:label>
                    <flux:input wire:model.number="travelersCount" type="number" min="1" max="20" />
                    <flux:error name="travelersCount" />
                </flux:field>

                <flux:field>
                    <flux:label>نوع الغرفة المفضل</flux:label>
                    <flux:select wire:model="preferredRoomType">
                        <option value="">اختر نوع الغرفة</option>
                        @foreach ($this->roomTypes() as $roomType)
                            <option value="{{ $roomType['value'] }}">{{ $roomType['label'] }}</option>
                        @endforeach
                    </flux:select>
                    <flux:error name="preferredRoomType" />
                </flux:field>

                <flux:field>
                    <flux:label>ملاحظات <span class="text-zinc-500 text-xs">(اختياري)</span></flux:label>
                    <flux:textarea wire:model="notes" rows="4" placeholder="أي ملاحظات إضافية..." />
                    <flux:error name="notes" />
                </flux:field>

                <div class="pt-2">
                    <flux:button type="submit" variant="primary" wire:click="submit" wire:loading.attr="disabled"
                        class="w-full">
                        <span wire:loading.remove wire:target="submit">إرسال الطلب</span>
                        <span wire:loading wire:target="submit" class="flex items-center gap-2">
                            <span
                                class="animate-spin inline-block size-4 border-2 border-white/30 border-t-white rounded-full"></span>
                            جاري الإرسال...
                        </span>
                    </flux:button>
                </div>
            </div>
        </flux:card>
    @endif
</div>