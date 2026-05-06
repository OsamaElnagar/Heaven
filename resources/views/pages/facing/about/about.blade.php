<div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8" dir="rtl">

    {{-- Hero --}}
    <div class="mb-16 text-center">
        <flux:heading size="2xl" class="mb-4">من نحن</flux:heading>
        <p class="mx-auto max-w-2xl text-lg leading-relaxed text-zinc-600 dark:text-zinc-400">
            شركة هيفن للسياحة هي إحدى الشركات الرائدة في تنظيم رحلات الحج والعمرة.
            نفتخر بخبرة تمتد لأكثر من 15 عاماً في خدمة ضيوف الرحمن.
        </p>
    </div>

    {{-- Licenses --}}
    <div class="mb-16">
        <flux:heading size="xl" class="mb-8 text-center">تراخيصنا واعتماداتنا</flux:heading>
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <div
                class="rounded-xl border border-zinc-200 bg-white p-6 text-center shadow-sm dark:border-zinc-700 dark:bg-zinc-800 dark:shadow-none">
                <div
                    class="mx-auto mb-4 flex size-14 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/30">
                    <flux:icon.building-office class="size-7 text-emerald-600" />
                </div>
                <h3 class="mb-2 text-lg font-bold">وزارة السياحة</h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">رقم الترخيص: 12345</p>
            </div>
            <div
                class="rounded-xl border border-zinc-200 bg-white p-6 text-center shadow-sm dark:border-zinc-700 dark:bg-zinc-800 dark:shadow-none">
                <div
                    class="mx-auto mb-4 flex size-14 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/30">
                    <flux:icon.globe-alt class="size-7 text-emerald-600" />
                </div>
                <h3 class="mb-2 text-lg font-bold">وزارة الحج والعمرة</h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">رقم الترخيص: 67890</p>
            </div>
            <div
                class="rounded-xl border border-zinc-200 bg-white p-6 text-center shadow-sm dark:border-zinc-700 dark:bg-zinc-800 dark:shadow-none">
                <div
                    class="mx-auto mb-4 flex size-14 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/30">
                    <flux:icon.document-check class="size-7 text-emerald-600" />
                </div>
                <h3 class="mb-2 text-lg font-bold">غرفة شركات السياحة</h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">عضوية رقم: 11111</p>
            </div>
        </div>
    </div>

    {{-- Vision & Mission --}}
    <div class="mb-16 grid grid-cols-1 gap-8 md:grid-cols-2">
        <div
            class="rounded-xl border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-700 dark:bg-zinc-800 dark:shadow-none">
            <div class="mb-4 flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/30">
                    <flux:icon.star class="size-5 text-emerald-600" />
                </div>
                <flux:heading size="lg">رؤيتنا</flux:heading>
            </div>
            <p class="leading-relaxed text-zinc-600 dark:text-zinc-400">
                أن نكون الخيار الأول للحاج والمعتمر في مصر والوطن العربي.
            </p>
        </div>
        <div
            class="rounded-xl border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-700 dark:bg-zinc-800 dark:shadow-none">
            <div class="mb-4 flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/30">
                    <flux:icon.information-circle class="size-5 text-emerald-600" />
                </div>
                <flux:heading size="lg">رسالتنا</flux:heading>
            </div>
            <p class="leading-relaxed text-zinc-600 dark:text-zinc-400">
                تقديم خدمات حج وعمرة متميزة تجمع بين الجودة والسعر المناسب، مع الالتزام بأعلى معايير
                السلامة والراحة لضيوف الرحمن.
            </p>
        </div>
    </div>

    {{-- Office Location --}}
    <div>
        <flux:heading size="xl" class="mb-8 text-center">مكتبنا</flux:heading>
        <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
            <div
                class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-800 dark:shadow-none">
                <div class="mb-4 flex items-center gap-2 text-zinc-600 dark:text-zinc-400">
                    <flux:icon.map-pin class="size-5 text-emerald-600" />
                    <span class="font-medium">العنوان</span>
                </div>
                <p class="mb-6 leading-relaxed text-zinc-600 dark:text-zinc-400">
                    القاهرة، مصر - شارع التسعين، التجمع الخامس
                </p>

                <div class="mb-4 flex items-center gap-2 text-zinc-600 dark:text-zinc-400">
                    <flux:icon.clock class="size-5 text-emerald-600" />
                    <span class="font-medium">ساعات العمل</span>
                </div>
                <div class="space-y-2 text-sm text-zinc-600 dark:text-zinc-400">
                    <div class="flex justify-between">
                        <span>السبت - الخميس</span>
                        <span>9:00 ص - 9:00 م</span>
                    </div>
                    <div class="flex justify-between">
                        <span>الجمعة</span>
                        <span>مغلق</span>
                    </div>
                </div>
            </div>
            <div
                class="flex items-center justify-center rounded-xl border-2 border-dashed border-zinc-300 bg-zinc-100 p-8 dark:border-zinc-600 dark:bg-zinc-800">
                <div class="text-center">
                    <flux:icon.map-pin class="mx-auto size-12 text-zinc-400" />
                    <p class="mt-3 text-zinc-500 dark:text-zinc-400">خريطة الموقع</p>
                </div>
            </div>
        </div>
    </div>

</div>