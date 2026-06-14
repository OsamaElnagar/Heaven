<?php

namespace App\Filament\Resources\PayrollRuns\Widgets;

use App\Models\PayrollLine;
use App\Models\PayrollRun;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PayrollRunsStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $runIds = PayrollRun::pluck('id');
        $totalRuns = PayrollRun::count();
        $totalLines = PayrollLine::whereIn('payroll_run_id', $runIds)->count();
        $totalGross = PayrollLine::whereIn('payroll_run_id', $runIds)->sum('gross_salary');
        $totalDeductions = PayrollLine::whereIn('payroll_run_id', $runIds)->sum('social_insurance') +
            PayrollLine::whereIn('payroll_run_id', $runIds)->sum('income_tax') +
            PayrollLine::whereIn('payroll_run_id', $runIds)->sum('advances_deduction') +
            PayrollLine::whereIn('payroll_run_id', $runIds)->sum('other_deductions');
        $totalNet = PayrollLine::whereIn('payroll_run_id', $runIds)->sum('net_salary');
        $totalPaid = PayrollLine::whereIn('payroll_run_id', $runIds)->sum('paid_amount');

        return [
            Stat::make('المسيرات', $totalRuns)
                ->icon('heroicon-o-document-text'),
            Stat::make('الموظفون', $totalLines)
                ->icon('heroicon-o-users'),
            Stat::make('إجمالي المستحقات', number_format($totalGross))
                ->icon('heroicon-o-banknotes')
                ->color('success'),
            Stat::make('إجمالي الخصومات', number_format($totalDeductions))
                ->icon('heroicon-o-arrow-trending-down')
                ->color('danger'),
            Stat::make('صافي الرواتب', number_format($totalNet))
                ->icon('heroicon-o-calculator')
                ->color('info'),
            Stat::make('المتبقي', number_format($totalNet - $totalPaid))
                ->icon('heroicon-o-clock')
                ->color($totalNet - $totalPaid <= 0 ? 'success' : 'warning'),
        ];
    }
}
