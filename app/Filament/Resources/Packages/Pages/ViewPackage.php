<?php

namespace App\Filament\Resources\Packages\Pages;

use App\Filament\Resources\Packages\Actions\ExportPackageSummaryAction;
use App\Filament\Resources\Packages\PackageResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPackage extends ViewRecord
{
    protected static string $resource = PackageResource::class;

    protected static ?string $title = 'عرض باقة';

    protected function getHeaderActions(): array
    {
        return [
            ExportPackageSummaryAction::make(),
            EditAction::make()->label('تعديل'),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
