<?php

namespace App\Filament\Widgets;

use App\Enums\ExpenseStatus;
use App\Models\ReceiptVoucher;
use Filament\Forms\Components\Select;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class RevenueChartWidget extends ChartWidget
{
    protected ?string $heading = 'الإيرادات الشهرية';

    protected string $color = 'success';

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '380px';

    public ?int $year = null;

    protected function getType(): string
    {
        return 'bar';
    }

    public function getFormSchema(): array
    {
        return [
            Select::make('year')
                ->label('السنة')
                ->options(fn () => collect(range(now()->year, now()->year - 5))->mapWithKeys(fn ($y) => [$y => $y]))
                ->default(now()->year)
                ->live()
                ->selectablePlaceholder(false),
        ];
    }

    protected function getData(): array
    {
        $year = $this->year ?? now()->year;
        $cacheKey = "revenue_chart_{$year}";

        $data = Cache::remember($cacheKey, 300, function () use ($year) {
            $monthly = ReceiptVoucher::where('status', ExpenseStatus::POSTED)
                ->whereYear('voucher_date', $year)
                ->selectRaw("CAST(strftime('%m', voucher_date) AS INTEGER) as month, SUM(amount) as total")
                ->groupBy('month')
                ->pluck('total', 'month')
                ->toArray();

            return array_map(fn ($m) => (float) ($monthly[$m] ?? 0), range(1, 12));
        });

        return [
            'datasets' => [
                [
                    'label' => 'الإيرادات (ج.م)',
                    'data' => $data,
                    'backgroundColor' => '#10b981',
                    'borderColor' => '#059669',
                    'borderWidth' => 0,
                    'borderRadius' => 4,
                ],
            ],
            'labels' => [
                'يناير',
                'فبراير',
                'مارس',
                'إبريل',
                'مايو',
                'يونيو',
                'يوليو',
                'أغسطس',
                'سبتمبر',
                'أكتوبر',
                'نوفمبر',
                'ديسمبر',
            ],
        ];
    }
}
