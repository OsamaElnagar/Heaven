<?php

namespace App\Filament\Resources\Safes\Pages;

use App\Filament\Resources\Safes\SafeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSafe extends CreateRecord
{
    protected static string $resource = SafeResource::class;

    protected static ?string $title = 'إنشاء خزينة';
}
