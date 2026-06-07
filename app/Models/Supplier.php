<?php

namespace App\Models;

use App\Enums\SupplierType;
use App\Models\Concerns\HasEntityCode;
use App\Observers\PartyAccountObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy([PartyAccountObserver::class])]
class Supplier extends Model
{
    use HasEntityCode;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'type',
        'country',
        'city',
        'contact_person',
        'phone',
        'email',
        'notes',
        'account_id',
    ];

    protected $casts = [
        'type' => SupplierType::class,
    ];

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    public function hotels(): HasMany
    {
        return $this->hasMany(Hotel::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function journalLines(): HasMany
    {
        return $this->hasMany(JournalLine::class);
    }

    public static function entityCodeType(): string
    {
        return 'SUP';
    }
}
