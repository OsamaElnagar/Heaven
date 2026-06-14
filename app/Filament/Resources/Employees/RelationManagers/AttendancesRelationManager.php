<?php

namespace App\Filament\Resources\Employees\RelationManagers;

use App\Enums\AttendanceStatus;
use App\Filament\Resources\Attendances\Tables\AttendancesTable;
use App\Filament\Resources\Employees\Actions\MarkTodayAttendanceAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class AttendancesRelationManager extends RelationManager
{
    protected static string $relationship = 'attendances';

    protected static ?string $title = 'الحضور والانصراف';

    protected static ?string $modelLabel = 'سجل حضور';

    protected static ?string $pluralModelLabel = 'سجلات حضور';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('date')
                    ->required()
                    ->default(now()),
                TimePicker::make('check_in')
                    ->label('وقت الحضور'),
                TimePicker::make('check_out')
                    ->label('وقت الانصراف'),
                Select::make('status')
                    ->options(AttendanceStatus::class)
                    ->required()
                    ->default('present'),
                TextInput::make('overtime_hours')
                    ->label('ساعات إضافية')
                    ->numeric()
                    ->default(0),
                Textarea::make('notes')
                    ->label('ملاحظات'),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return AttendancesTable::configure($table)
            ->headerActions([
                MarkTodayAttendanceAction::make()->record($this->getOwnerRecord()),
            ]);
    }
}
