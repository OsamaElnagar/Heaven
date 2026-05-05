<?php

namespace App\Models;

use App\Enums\SupplierType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'country',
        'city',
        'contact_person',
        'phone',
        'email',
        'notes',
    ];

    protected $casts = [
        'type' => SupplierType::class,
    ];

    public function hotels(): HasMany
    {
        return $this->hasMany(Hotel::class);
    }
}
