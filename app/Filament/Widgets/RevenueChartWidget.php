<?php

namespace App\Filament\Widgets;

use App\Enums\ExpenseStatus;
use App\Models\ReceiptVoucher;
use Filament\Widgets\ChartWidget;

class RevenueChartWidget extends ChartWidget
{
    protected ?string $heading = 'الإيرادات الشهرية';

    protected string $color = 'success';

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '380px';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $year = now()->year;
        $data = [];

        for ($m = 1; $m <= 12; $m++) {
            $data[] = (float) ReceiptVoucher::where('status', ExpenseStatus::POSTED)
                ->whereYear('voucher_date', $year)
                ->whereMonth('voucher_date', $m)
                ->sum('amount');
        }

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
