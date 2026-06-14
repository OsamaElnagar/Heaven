<?php

namespace App\Filament\Resources\Attendances\Actions;

use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

class BulkMarkWeekendAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'bulkMarkWeekend';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('تعليم أيام الجمعة')
            ->icon('heroicon-o-calendar-days')
            ->color('gray')
            ->slideOver()
            ->schema([
                Select::make('month')
                    ->label('الشهر')
                    ->options([
                        1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
                        5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
                        9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر',
                    ])
                    ->required()
                    ->default(now()->month),
                TextInput::make('year')
                    ->label('السنة')
                    ->numeric()
                    ->required()
                    ->default(now()->year),
            ])
            ->action(function (array $data): void {
                $month = (int) $data['month'];
                $year = (int) $data['year'];

                $fridays = $this->getFridaysInMonth($month, $year);

                if (empty($fridays)) {
                    Notification::make()
                        ->title('لا توجد أيام جمعة في هذا الشهر')
                        ->warning()
                        ->send();

                    return;
                }

                $employees = Employee::where('is_active', true)->get();
                $now = now();
                $records = [];
                $skipped = 0;

                foreach ($fridays as $friday) {
                    $existingDates = Attendance::where('date', $friday)
                        ->whereIn('employee_id', $employees->pluck('id'))
                        ->pluck('employee_id')
                        ->toArray();

                    foreach ($employees as $employee) {
                        if (! in_array($employee->id, $existingDates)) {
                            $records[] = [
                                'employee_id' => $employee->id,
                                'date' => $friday,
                                'status' => 'weekend',
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];
                        } else {
                            $skipped++;
                        }
                    }
                }

                if (! empty($records)) {
                    Attendance::insert($records);
                }

                Notification::make()
                    ->title('تم تعليم '.count($records).' تسجيل كعطلة أسبوعية (تم تخطي '.$skipped.' مسجل مسبقاً)')
                    ->success()
                    ->send();
            });
    }

    private function getFridaysInMonth(int $month, int $year): array
    {
        $fridays = [];
        $date = Carbon::create($year, $month, 1);
        $daysInMonth = $date->daysInMonth;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $current = Carbon::create($year, $month, $day);
            if ($current->isFriday()) {
                $fridays[] = $current->format('Y-m-d');
            }
        }

        return $fridays;
    }
}
