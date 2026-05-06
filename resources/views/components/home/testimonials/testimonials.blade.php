<section class="bg-zinc-50 py-16 dark:bg-zinc-900 sm:py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <flux:heading size="xl" class="text-zinc-900 dark:text-white">آراء عملائنا</flux:heading>
            <flux:text class="mt-3 text-zinc-500 dark:text-zinc-400">ماذا يقول ضيوف الرحمن عن تجربتهم معنا</flux:text>
        </div>

        @php
        $testimonials = [
            [
                'name' => 'أحمد المحمد',
                'city' => 'الرياض',
                'quote' => 'تجربة رائعة مع هيفن، التنظيم كان ممتازاً والفنادق قريبة من الحرم. أنصح الجميع بالتعامل معهم.',
            ],
            [
                'name' => 'فاطمة العتيبي',
                'city' => 'جدة',
                'quote' => 'أديت العمرة مع عائلتي عبر هيفن وكانت الخدمة أكثر من رائعة. شكراً لفريق العمل على حسن التعامل.',
            ],
            [
                'name' => 'محمد القحطاني',
                'city' => 'الدمام',
                'quote' => 'حج هذا العام كان أفضل تجربة في حياتي بفضل تنظيم هيفن. كل شيء كان مرتباً من البداية حتى النهاية.',
            ],
            [
                'name' => 'نورة السبيعي',
                'city' => 'مكة المكرمة',
                'quote' => 'خدمة عملاء ممتازة وتجاوب سريع مع جميع الاستفسارات. الحجز كان سهلاً والرحلة كانت مريحة جداً.',
            ],
        ];
        @endphp

        <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($testimonials as $testimonial)
                <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
                    <div class="mb-4 flex gap-0.5">
                        @for ($i = 0; $i < 5; $i++)
                            <flux:icon.star class="size-4 text-amber-400" variant="solid" />
                        @endfor
                    </div>

                    <blockquote class="mb-4 text-sm leading-relaxed text-zinc-600 dark:text-zinc-300">
                        &ldquo;{{ $testimonial['quote'] }}&rdquo;
                    </blockquote>

                    <div class="flex items-center gap-3 border-t border-zinc-100 pt-4 dark:border-zinc-700">
                        <div class="flex size-10 items-center justify-center rounded-full bg-emerald-100 text-sm font-bold text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-400">
                            {{ mb_substr($testimonial['name'], 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $testimonial['name'] }}</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $testimonial['city'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
