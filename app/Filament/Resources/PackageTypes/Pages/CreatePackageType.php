<?php

namespace App\Filament\Resources\PackageTypes\Pages;

use App\Filament\Resources\PackageTypes\PackageTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePackageType extends CreateRecord
{
    protected static string $resource = PackageTypeResource::class;

    protected static ?string $title = 'نوع باقة جديد';
}
