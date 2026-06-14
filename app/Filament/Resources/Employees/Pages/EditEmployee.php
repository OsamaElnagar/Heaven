<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Resources\Employees\Actions\MarkTodayAttendanceAction;
use App\Filament\Resources\Employees\EmployeeResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Colors\Color;

class EditEmployee extends EditRecord
{
    protected static string $resource = EmployeeResource::class;

    protected static ?string $title = 'تعديل موظف';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('statement')
                ->label('كشف حساب')
                ->icon('heroicon-o-document-text')
                ->url(fn ($record) => EmployeeResource::getUrl('accounting-statement', ['record' => $record])),
            MarkTodayAttendanceAction::make(),
            Action::make('call')
                ->label('اتصال')
                ->icon('heroicon-m-phone')
                ->url(fn ($record) => $record->phone ? 'tel:'.$record->phone : null)
                ->hidden(fn ($record) => blank($record->phone))
                ->color(Color::Cyan),
            Action::make('whatsapp')
                ->label('واتساب')
                ->icon('heroicon-m-chat-bubble-left-right')
                ->color('success')
                ->url(function ($record) {
                    if (blank($record->phone)) {
                        return null;
                    }

                    $phone = preg_replace('/\D+/', '', $record->phone);

                    if (! str_starts_with($phone, '2')) {
                        $phone = '2'.$phone;
                    }

                    return 'https://wa.me/'.$phone;
                })
                ->openUrlInNewTab()
                ->hidden(fn ($record) => blank($record->phone)),
            ViewAction::make()->label('عرض'),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
