<?php

namespace App\Filament\Resources\Attendances\Actions;

use App\Enums\AttendanceStatus;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;

class BulkAttendanceAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'bulkAttendance';
    }

    public static function calculateOvertime(string $checkIn, string $checkOut, float $dailyHours): float
    {
        $start = Carbon::parse($checkIn);
        $end = Carbon::parse($checkOut);

        $actualHours = max(0, $end->diffInMinutes($start) / 60);

        return max(0, round($actualHours - $dailyHours, 2));
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('تسجيل حضور جماعي')
            ->modalHeading('تسجيل الحضور الجماعي')
            ->modalWidth('7xl')
            ->icon('heroicon-o-clipboard-document-check')
            ->color('success')
            ->slideOver()
            ->schema([
                DatePicker::make('attendance_date')
                    ->label('تاريخ الحضور')
                    ->default(now())
                    ->required()
                    ->live(),
                Repeater::make('employees')
                    ->label('الموظفون')
                    ->schema([
                        Hidden::make('employee_id'),
                        TextInput::make('name')
                            ->label('الموظف')
                            ->disabled()
                            ->dehydrated(false),
                        Select::make('status')
                            ->options(AttendanceStatus::class)
                            ->required()
                            ->default('present')
                            ->label('الحالة'),
                        TimePicker::make('check_in')
                            ->label('وقت الحضور')
                            ->displayFormat('h:i a')
                            ->default('09:00'),
                        TimePicker::make('check_out')
                            ->label('وقت الانصراف')
                            ->displayFormat('h:i a')
                            ->default('17:00'),
                        TextInput::make('overtime_hours')
                            ->label('ساعات إضافية')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(5)
                    ->columnSpanFull(),
            ])
            ->action(function (array $data): void {
                $date = $data['attendance_date'];
                $employeeData = collect($data['employees'] ?? []);

                $employeeIds = $employeeData->pluck('employee_id')->toArray();
                $employees = Employee::whereIn('id', $employeeIds)->get()->keyBy('id');

                $records = [];
                $now = now();

                foreach ($employeeData as $row) {
                    $employeeId = $row['employee_id'];
                    $checkIn = $row['check_in'] ?? null;
                    $checkOut = $row['check_out'] ?? null;
                    $dailyHours = $employees->get($employeeId)?->daily_hours ?? 8;

                    $records[] = [
                        'employee_id' => $employeeId,
                        'date' => $date,
                        'check_in' => $checkIn,
                        'check_out' => $checkOut,
                        'status' => $row['status'] ?? 'present',
                        'overtime_hours' => $checkIn && $checkOut
                            ? static::calculateOvertime($checkIn, $checkOut, $dailyHours)
                            : 0,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                Attendance::upsert(
                    $records,
                    ['employee_id', 'date'],
                    ['check_in', 'check_out', 'status', 'overtime_hours', 'updated_at']
                );

                Notification::make()
                    ->title('تم تسجيل الحضور لـ '.count($records).' موظف بنجاح')
                    ->success()
                    ->send();
            })
            ->mountUsing(function (Schema $form): void {
                $date = now()->format('Y-m-d');

                $employees = Employee::where('is_active', true)->get();

                $existingAttendances = Attendance::where('date', $date)
                    ->whereIn('employee_id', $employees->pluck('id'))
                    ->get()
                    ->keyBy('employee_id');

                $employeeRows = [];
                foreach ($employees as $employee) {
                    $existing = $existingAttendances->get($employee->id);

                    $checkIn = $existing?->check_in?->format('H:i') ?? '09:00';
                    $checkOut = $existing?->check_out?->format('H:i') ?? '17:00';

                    $employeeRows[$employee->id] = [
                        'employee_id' => $employee->id,
                        'name' => $employee->name,
                        'status' => $existing?->status->value ?? 'present',
                        'check_in' => $checkIn,
                        'check_out' => $checkOut,
                        'overtime_hours' => $existing?->overtime_hours ?? static::calculateOvertime($checkIn, $checkOut, $employee->daily_hours ?? 8),
                    ];
                }

                $form->fill([
                    'attendance_date' => $date,
                    'employees' => $employeeRows,
                ]);
            });
    }
}
