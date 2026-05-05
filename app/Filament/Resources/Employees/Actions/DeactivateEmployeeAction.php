<?php

namespace App\Filament\Resources\Employees\Actions;

use App\Models\Employee;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class DeactivateEmployeeAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'deactivateEmployee';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(fn (Employee $record) => $record->is_active ? 'إلغاء التفعيل' : 'تفعيل')
            ->icon(fn (Employee $record) => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
            ->color(fn (Employee $record) => $record->is_active ? 'danger' : 'success')
            ->requiresConfirmation()
            ->modalHeading(fn (Employee $record) => $record->is_active ? 'إلغاء تفعيل الموظف' : 'تفعيل الموظف')
            ->action(function (Employee $record) {
                $record->update([
                    'is_active' => ! $record->is_active,
                    'left_at' => $record->is_active ? null : Carbon::today(),
                ]);
                Notification::make()->title('تم تحديث الحالة')->success()->send();
            });
    }
}
