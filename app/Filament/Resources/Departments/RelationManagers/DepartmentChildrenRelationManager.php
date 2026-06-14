<?php

namespace App\Filament\Resources\Departments\RelationManagers;

use App\Filament\Resources\Departments\DepartmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class DepartmentChildrenRelationManager extends RelationManager
{
    protected static string $relationship = 'children';

    protected static ?string $title = 'الأقسام الفرعية';

    protected static ?string $modelLabel = 'قسم فرعي';

    protected static ?string $pluralModelLabel = 'أقسام فرعية';

    protected static ?string $relatedResource = DepartmentResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
