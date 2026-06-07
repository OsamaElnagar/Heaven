<?php

namespace App\Models;

use App\Models\Concerns\HasEntityCode;
use App\Observers\PartyAccountObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy([PartyAccountObserver::class])]
class Safe extends Model
{
    use HasEntityCode;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'account_id',
        'responsible_employee_id',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function responsibleEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'responsible_employee_id');
    }

    public function journalLines(): HasMany
    {
        return $this->hasMany(JournalLine::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function entityCodeType(): string
    {
        return 'SAF';
    }
}
