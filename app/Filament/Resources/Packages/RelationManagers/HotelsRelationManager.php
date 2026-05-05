<?php

namespace App\Filament\Resources\Packages\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HotelsRelationManager extends RelationManager
{
    protected static string $relationship = 'hotels';

    protected static ?string $title = 'الفنادق';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('city')
                    ->label('المدينة')
                    ->options(['makkah' => 'مكة المكرمة', 'madinah' => 'المدينة المنورة'])
                    ->required()
                    ->native(false),
                TextInput::make('nights')
                    ->label('عدد الليالي')
                    ->required()
                    ->numeric(),
                TextInput::make('cost_per_person')
                    ->label('تكلفة الفرد')
                    ->numeric()
                    ->prefix('ج.م'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('الفندق')->searchable(),
                TextColumn::make('pivot.city')->label('المدينة')->badge(),
                TextColumn::make('pivot.nights')->label('الليالي'),
                TextColumn::make('pivot.cost_per_person')->label('تكلفة الفرد')->money('EGP'),
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Select::make('city')
                            ->options(['makkah' => 'مكة المكرمة', 'madinah' => 'المدينة المنورة'])
                            ->required(),
                        TextInput::make('nights')->numeric()->required()->label('عدد الليالي'),
                        TextInput::make('cost_per_person')->numeric()->label('تكلفة الفرد')->prefix('ج.م'),
                    ]),
            ])
            ->recordActions([
                DetachAction::make(),
            ])
            ->toolbarActions([
                DetachBulkAction::make(),
            ]);
    }
}
