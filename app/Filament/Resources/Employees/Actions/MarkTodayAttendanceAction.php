<?php

namespace App\Filament\Resources\Employees\Actions;

use App\Enums\AttendanceStatus;
use App\Models\Attendance;
use App\Models\Employee;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Notifications\Notification;

class MarkTodayAttendanceAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'markTodayAttendance';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('تسجيل حضور/غياب')
            ->icon('heroicon-o-check-circle')
            ->color('info')
            ->slideOver()
            ->schema([
                DatePicker::make('date')
                    ->label('التاريخ')
                    ->default(now())
                    ->required(),
                TimePicker::make('check_in')
                    ->label('وقت الحضور')
                    ->displayFormat('h:i a')
                    ->default('09:00'),
                TimePicker::make('check_out')
                    ->label('وقت الانصراف')
                    ->displayFormat('h:i a')
                    ->default('17:00'),
                Select::make('status')
                    ->options(AttendanceStatus::class)
                    ->required()
                    ->default(AttendanceStatus::PRESENT)
                    ->label('الحالة'),
                TextInput::make('overtime_hours')
                    ->label('ساعات إضافية')
                    ->numeric()
                    ->default(0),
            ])
            ->action(function (array $data, Employee $record): void {
                $existing = Attendance::where('employee_id', $record->id)
                    ->where('date', $data['date'])
                    ->first();

                if ($existing) {
                    $existing->update([
                        'check_in' => $data['check_in'] ?? null,
                        'check_out' => $data['check_out'] ?? null,
                        'status' => $data['status'],
                        'overtime_hours' => $data['overtime_hours'] ?? 0,
                    ]);

                    Notification::make()
                        ->title('تم تحديث تسجيل الحضور')
                        ->success()
                        ->send();
                } else {
                    Attendance::create([
                        'employee_id' => $record->id,
                        'date' => $data['date'],
                        'check_in' => $data['check_in'] ?? null,
                        'check_out' => $data['check_out'] ?? null,
                        'status' => $data['status'],
                        'overtime_hours' => $data['overtime_hours'] ?? 0,
                    ]);

                    Notification::make()
                        ->title('تم تسجيل الحضور بنجاح')
                        ->success()
                        ->send();
                }
            });
    }
}
