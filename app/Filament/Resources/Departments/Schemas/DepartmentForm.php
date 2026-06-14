<?php

namespace App\Filament\Resources\Departments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DepartmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات القسم')->components([
                    TextInput::make('name')
                        ->label('الاسم')
                        ->required(),
                    Select::make('parent_id')
                        ->label('القسم الأب')
                        ->relationship('parent', 'name'),
                    Toggle::make('is_active')->default(true)
                        ->label('نشط'),
                ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
