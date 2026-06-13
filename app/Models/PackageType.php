<?php

namespace App\Models;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PackageType extends Model implements HasColor, HasIcon, HasLabel
{
    protected $fillable = [
        'name',
        'name_ar',
        'slug',
        'color',
        'icon',
        'is_religious',
        'duration_nights_min',
        'duration_nights_max',
    ];

    protected $casts = [
        'is_religious' => 'boolean',
        'duration_nights_min' => 'integer',
        'duration_nights_max' => 'integer',
    ];

    public function getColor(): string|array|null
    {
        return $this->color;
    }

    public function getIcon(): string|\BackedEnum|Htmlable|null
    {
        return $this->icon;
    }

    public function getLabel(): string|Htmlable|null
    {
        return $this->name_ar;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }
}
