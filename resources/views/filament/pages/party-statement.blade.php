<x-filament-panels::page>
    <div class="space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
            <div>
                <h2 class="text-lg font-semibold">كشف حساب: {{ $entityName }}</h2>
                @if ($accountId)
                    <p class="text-sm text-gray-500">رقم الحساب: {{ $accountId }}</p>
                @else
                    <p class="text-sm text-red-500">لا يوجد حساب  مرتبط بهذا الطرف.</p>
                @endif
                @if ($from || $to)
                    <p class="text-sm text-gray-500">
                        الفترة:
                        {{ $from ?? '...' }}
                        إلى
                        {{ $to ?? '...' }}
                    </p>
                @endif
            </div>
            <div class="text-left">
                <div class="text-sm text-gray-500">رصيد افتتاحي</div>
                <div class="text-2xl font-bold">{{ number_format($openingBalance, 0) }}</div>
            </div>
        </div>

        {{ $this->table }}
    </div>
</x-filament-panels::page>
