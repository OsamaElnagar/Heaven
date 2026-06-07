<?php

namespace App\Filament\Resources\Employees\Actions;

use App\Models\Employee;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;

class LinkUserAccountAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'linkUserAccount';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('ربط حساب مستخدم')
            ->icon('heroicon-o-link')
            ->color('info')
            ->schema([
                Select::make('user_id')
                    ->label('المستخدم')
                    ->options(fn () => User::pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->native(false),
            ])
            ->modalHeading('ربط حساب مستخدم')
            ->visible(fn (Employee $record) => $record->user_id === null)
            ->action(function (Employee $record, array $data) {
                $record->update(['user_id' => $data['user_id']]);
                Notification::make()->title('تم ربط الحساب')->success()->send();
            });
    }
}
