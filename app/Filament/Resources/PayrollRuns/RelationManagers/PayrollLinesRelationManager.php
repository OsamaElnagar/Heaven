<?php

namespace App\Filament\Resources\PayrollRuns\RelationManagers;

use App\Filament\Resources\Employees\EmployeeResource;
use App\Filament\Resources\PayrollRuns\Actions\BulkMarkPaidAction;
use App\Filament\Resources\PayrollRuns\Actions\MarkPayrollLinePaidAction;
use App\Models\Employee;
use App\Services\PayrollService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PayrollLinesRelationManager extends RelationManager
{
    protected static string $relationship = 'lines';

    protected static ?string $title = 'بنود المسير';

    protected static ?string $modelLabel = 'بند مسير';

    protected static ?string $pluralModelLabel = 'بنود مسير';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('payroll_run_id'),
                Select::make('employee_id')
                    ->label('الموظف')
                    ->relationship('employee', 'name')
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                        if (! $state) {
                            return;
                        }

                        $employee = Employee::find($state);

                        if (! $employee) {
                            return;
                        }

                        $payrollRun = $this->getOwnerRecord();

                        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $payrollRun->month, $payrollRun->year);

                        $attendances = $employee->attendances()
                            ->whereMonth('date', $payrollRun->month)
                            ->whereYear('date', $payrollRun->year)
                            ->get();

                        $halfDayCount = $attendances->where('status', 'half_day')->count();
                        $attendanceDays = $attendances->whereIn('status', ['present', 'late', 'overtime'])->count() + $halfDayCount;
                        $absenceDays = $attendances->where('status', 'absent')->count();
                        $weekendHolidayDays = $attendances->whereIn('status', ['weekend', 'holiday'])->count();
                        $totalOvertimeHours = (float) $attendances->sum('overtime_hours');

                        $dailyHours = (float) ($employee->daily_hours ?? 8);

                        $workingDaysInMonth = $daysInMonth - $weekendHolidayDays;
                        $workingDaysInMonth = max($workingDaysInMonth, 1);

                        $effectiveAttendanceDays = $attendanceDays - ($halfDayCount * 0.5);

                        $proratedBase = $workingDaysInMonth > 0
                            ? (int) round(($employee->base_salary ?? 0) * $effectiveAttendanceDays / $workingDaysInMonth)
                            : 0;
                        $dailyRate = $workingDaysInMonth > 0
                            ? ($employee->base_salary ?? 0) / $workingDaysInMonth
                            : 0;
                        $hourlyRate = $dailyHours > 0 ? $dailyRate / $dailyHours : 0;
                        $overtimeValue = (int) round($totalOvertimeHours * $hourlyRate * 1.5);

                        $payrollService = app(PayrollService::class);

                        $socialInsurance = $payrollService->calculateSocialInsurance($proratedBase + $overtimeValue, $employee->type);
                        $incomeTax = $payrollService->calculateIncomeTax($proratedBase + $overtimeValue, $socialInsurance);

                        $advancesDeduction = 0;
                        foreach ($employee->employeeAdvances()->where('status', 'active')->get() as $advance) {
                            $outstanding = $advance->amount - $advance->repaid_amount;
                            if ($outstanding <= 0) {
                                continue;
                            }
                            $installment = $advance->installments > 0
                                ? (int) floor($advance->amount / $advance->installments)
                                : $outstanding;
                            $advancesDeduction += min($installment, $outstanding);
                        }

                        $gross = $proratedBase + $overtimeValue;
                        $net = $gross - $socialInsurance - $incomeTax - $advancesDeduction;

                        $set('base_salary', $proratedBase);
                        $set('days_in_month', $daysInMonth);
                        $set('attendance_days', $attendanceDays);
                        $set('absence_days', $absenceDays);
                        $set('overtime_hours', $totalOvertimeHours);
                        $set('overtime', $overtimeValue);
                        $set('social_insurance', $socialInsurance);
                        $set('income_tax', $incomeTax);
                        $set('advances_deduction', $advancesDeduction);
                        $set('gross_salary', $gross);
                        $set('net_salary', $net);
                        $set('remaining_amount', $net);
                    })
                    ->unique(modifyRuleUsing: function ($record, $state) {
                        return fn (Builder $query) => $query
                            ->where('payroll_run_id', $this->getOwnerRecord()->id)
                            ->when($record, fn ($q) => $q->where('id', '!=', $record->id));
                    }),
                TextInput::make('days_in_month')
                    ->label('أيام الشهر')
                    ->numeric()
                    ->default(30)
                    ->readOnly(),
                TextInput::make('attendance_days')
                    ->label('أيام الحضور')
                    ->numeric()
                    ->readOnly(),
                TextInput::make('absence_days')
                    ->label('أيام الغياب')
                    ->numeric()
                    ->readOnly(),
                TextInput::make('base_salary')
                    ->label('الراتب الأساسي')
                    ->numeric()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Get $get, Set $set) => $this->recalculate($get, $set)),
                TextInput::make('allowances')
                    ->label('البدلات')
                    ->numeric()
                    ->default(0)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Get $get, Set $set) => $this->recalculate($get, $set)),
                TextInput::make('overtime_hours')
                    ->label('ساعات العمل الإضافي')
                    ->numeric()
                    ->readOnly(),
                TextInput::make('overtime')
                    ->label('قيمة الإضافي')
                    ->numeric()
                    ->default(0)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Get $get, Set $set) => $this->recalculate($get, $set)),
                TextInput::make('bonuses')
                    ->label('الحوافز')
                    ->numeric()
                    ->default(0)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Get $get, Set $set) => $this->recalculate($get, $set)),
                TextInput::make('gross_salary')
                    ->label('إجمالي المستحق')
                    ->numeric()
                    ->readOnly()
                    ->dehydrated(true),
                TextInput::make('social_insurance')
                    ->label('التأمين الاجتماعي')
                    ->numeric()
                    ->default(0)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Get $get, Set $set) => $this->recalculate($get, $set)),
                TextInput::make('income_tax')
                    ->label('ضريبة الدخل')
                    ->numeric()
                    ->default(0)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Get $get, Set $set) => $this->recalculate($get, $set)),
                TextInput::make('advances_deduction')
                    ->label('خصم السلف')
                    ->numeric()
                    ->default(0)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Get $get, Set $set) => $this->recalculate($get, $set)),
                TextInput::make('other_deductions')
                    ->label('خصومات أخرى')
                    ->numeric()
                    ->default(0)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Get $get, Set $set) => $this->recalculate($get, $set)),
                TextInput::make('net_salary')
                    ->label('الصافي')
                    ->numeric()
                    ->readOnly()
                    ->dehydrated(true),
                TextInput::make('paid_amount')
                    ->label('المدفوع')
                    ->numeric()
                    ->default(0)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Get $get, Set $set) => $this->recalculate($get, $set)),
                TextInput::make('remaining_amount')
                    ->label('المتبقي')
                    ->numeric()
                    ->readOnly()
                    ->dehydrated(true),
                Toggle::make('is_paid')
                    ->label('مدفوع'),
                Select::make('safe_id')
                    ->label('الخزنة')
                    ->relationship('safe', 'name'),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.name')
                    ->label('الموظف')
                    ->searchable()
                    ->url(fn ($record) => EmployeeResource::getUrl('edit', ['record' => $record->employee_id]))
                    ->toggleable(),
                TextColumn::make('employee.job_title')
                    ->label('المسمى الوظيفي')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('base_salary')
                    ->label('الراتب الأساسي')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('attendance_days')
                    ->label('أيام الحضور')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('absence_days')
                    ->label('أيام الغياب')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('overtime_hours')
                    ->label('ساعات العمل الإضافي')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('overtime')
                    ->label('قيمة الإضافي')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('bonuses')
                    ->label('الحوافز')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('gross_salary')
                    ->label('إجمالي المستحق')
                    ->numeric()
                    ->toggleable(),
                TextColumn::make('social_insurance')
                    ->label('التأمين الاجتماعي')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('income_tax')
                    ->label('ضريبة الدخل')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('advances_deduction')
                    ->label('خصم السلف')
                    ->numeric()
                    ->toggleable(),
                TextColumn::make('other_deductions')
                    ->label('خصومات أخرى')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('net_salary')
                    ->label('الصافي')
                    ->numeric()
                    ->toggleable(),
                TextColumn::make('paid_amount')
                    ->label('المدفوع')
                    ->numeric()
                    ->toggleable(),
                TextColumn::make('remaining_amount')
                    ->label('المتبقي')
                    ->numeric()
                    ->toggleable(),
                IconColumn::make('is_paid')
                    ->label('مدفوع')
                    ->boolean()
                    ->toggleable(),
            ])
            ->defaultSort('id')
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                MarkPayrollLinePaidAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkMarkPaidAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(25);
    }

    public function recalculate(Get $get, Set $set): void
    {
        $base = (int) ($get('base_salary') ?? 0);
        $allowances = (int) ($get('allowances') ?? 0);
        $overtime = (int) ($get('overtime') ?? 0);
        $bonuses = (int) ($get('bonuses') ?? 0);
        $socialInsurance = (int) ($get('social_insurance') ?? 0);
        $incomeTax = (int) ($get('income_tax') ?? 0);
        $advancesDeduction = (int) ($get('advances_deduction') ?? 0);
        $otherDeductions = (int) ($get('other_deductions') ?? 0);
        $paidAmount = (int) ($get('paid_amount') ?? 0);

        $gross = $base + $allowances + $overtime + $bonuses;
        $net = $gross - $socialInsurance - $incomeTax - $advancesDeduction - $otherDeductions;
        $remaining = $net - $paidAmount;

        $set('gross_salary', $gross);
        $set('net_salary', $net);
        $set('remaining_amount', $remaining);
        $set('is_paid', $remaining <= 0);
    }
}
