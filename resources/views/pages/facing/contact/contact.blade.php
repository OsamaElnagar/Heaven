<div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8" dir="rtl">

    <div class="mb-12 text-center">
        <flux:heading size="2xl" class="mb-4">اتصل بنا</flux:heading>
        <p class="text-zinc-500 dark:text-zinc-400">نحن هنا لخدمتكم، لا تترددوا في التواصل معنا</p>
    </div>

    <div class="grid grid-cols-1 gap-10 md:grid-cols-2">

        {{-- Contact Info --}}
        <div class="space-y-6">
            <div
                class="flex items-start gap-4 rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-800 dark:shadow-none">
                <div
                    class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/30">
                    <flux:icon.phone class="size-5 text-emerald-600" />
                </div>
                <div>
                    <h3 class="font-semibold">الهاتف</h3>
                    <p class="text-zinc-600 dark:text-zinc-400">+966 5X XXX XXXX</p>
                </div>
            </div>

            <div
                class="flex items-start gap-4 rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-800 dark:shadow-none">
                <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-green-100">
                    <svg class="size-5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold">واتساب</h3>
                    <a href="https://wa.me/9665XXXXXXXX" target="_blank"
                        class="text-green-600 hover:underline dark:text-green-400">
                        تواصل عبر واتساب
                    </a>
                </div>
            </div>

            <div
                class="flex items-start gap-4 rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-800 dark:shadow-none">
                <div
                    class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/30">
                    <flux:icon.envelope class="size-5 text-emerald-600" />
                </div>
                <div>
                    <h3 class="font-semibold">البريد الإلكتروني</h3>
                    <p class="text-zinc-600 dark:text-zinc-400">{{config('app.email')}}</p>
                </div>
            </div>

            <div
                class="flex items-start gap-4 rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-800 dark:shadow-none">
                <div
                    class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/30">
                    <flux:icon.map-pin class="size-5 text-emerald-600" />
                </div>
                <div>
                    <h3 class="font-semibold">العنوان</h3>
                    <p class="text-zinc-600 dark:text-zinc-400">القاهرة، مصر - شارع التسعين، التجمع الخامس</p>
                </div>
            </div>

            <div
                class="flex items-start gap-4 rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-800 dark:shadow-none">
                <div
                    class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/30">
                    <flux:icon.clock class="size-5 text-emerald-600" />
                </div>
                <div>
                    <h3 class="font-semibold">ساعات العمل</h3>
                    <p class="text-zinc-600 dark:text-zinc-400">السبت - الخميس: 9:00 ص - 9:00 م</p>
                    <p class="text-zinc-600 dark:text-zinc-400">الجمعة: مغلق</p>
                </div>
            </div>
        </div>

        {{-- Contact Form --}}
        <div>
            <div
                class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-800 dark:shadow-none">
                <flux:heading size="lg" class="mb-6">أرسل رسالة</flux:heading>

                @if ($sent)
                    <div class="rounded-xl bg-emerald-50 p-8 text-center dark:bg-emerald-950">
                        <div
                            class="mx-auto mb-4 flex size-14 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/30">
                            <flux:icon.check-badge class="size-7 text-emerald-600" />
                        </div>
                        <p class="text-lg font-semibold text-emerald-700 dark:text-emerald-300">تم إرسال رسالتك بنجاح</p>
                        <p class="mt-1 text-zinc-500 dark:text-zinc-400">سنتواصل معك قريباً</p>
                    </div>
                @else
                    <div class="space-y-5">
                        <flux:field>
                            <flux:label>الاسم</flux:label>
                            <flux:input wire:model="name" placeholder="الاسم الكامل" />
                            <flux:error name="name" />
                        </flux:field>

                        <flux:field>
                            <flux:label>رقم الجوال</flux:label>
                            <flux:input wire:model="phone" placeholder="05xxxxxxxx" />
                            <flux:error name="phone" />
                        </flux:field>

                        <flux:field>
                            <flux:label>البريد الإلكتروني <span class="text-xs text-zinc-400">(اختياري)</span></flux:label>
                            <flux:input wire:model="email" type="email" placeholder="example@domain.com" />
                            <flux:error name="email" />
                        </flux:field>

                        <flux:field>
                            <flux:label>الرسالة</flux:label>
                            <flux:textarea wire:model="message" rows="5" placeholder="اكتب رسالتك هنا..." />
                            <flux:error name="message" />
                        </flux:field>

                        <flux:button type="submit" variant="primary" wire:click="submit" class="w-full">
                            إرسال
                        </flux:button>
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- Map Placeholder --}}
    <div class="mt-12">
        <div
            class="flex h-64 items-center justify-center rounded-xl border-2 border-dashed border-zinc-300 bg-zinc-100 dark:border-zinc-600 dark:bg-zinc-800">
            <div class="text-center">
                <flux:icon.map-pin class="mx-auto size-10 text-zinc-400" />
                <p class="mt-2 text-zinc-500 dark:text-zinc-400">خريطة الموقع</p>
            </div>
        </div>
    </div>

</div>