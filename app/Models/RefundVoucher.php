<?php

namespace App\Models;

use App\Enums\ExpenseStatus;
use App\Enums\RefundPartyType;
use App\Enums\VoucherPaymentMethod;
use App\Models\Concerns\HasDocumentNumber;
use App\Observers\RefundVoucherObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy([RefundVoucherObserver::class])]
class RefundVoucher extends Model
{
    use HasDocumentNumber;
    use SoftDeletes;

    protected $fillable = [
        'number',
        'voucher_date',
        'payment_method',
        'safe_id',
        'bank_account_id',
        'cheque_number',
        'cheque_date',
        'amount',
        'party_type',
        'client_id',
        'supplier_id',
        'booking_id',
        'reference',
        'description',
        'attachment',
        'status',
        'journal_entry_id',
        'created_by',
        'posted_by',
        'posted_at',
    ];

    protected $casts = [
        'payment_method' => VoucherPaymentMethod::class,
        'party_type' => RefundPartyType::class,
        'status' => ExpenseStatus::class,
        'voucher_date' => 'date',
        'cheque_date' => 'date',
        'amount' => 'integer',
        'posted_at' => 'datetime',
    ];

    public function safe(): BelongsTo
    {
        return $this->belongsTo(Safe::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', ExpenseStatus::DRAFT);
    }

    public function scopePosted($query)
    {
        return $query->where('status', ExpenseStatus::POSTED);
    }

    public function isForClient(): bool
    {
        return $this->party_type === RefundPartyType::CLIENT;
    }

    public function isForSupplier(): bool
    {
        return $this->party_type === RefundPartyType::SUPPLIER;
    }

    public function isPosted(): bool
    {
        return $this->status === ExpenseStatus::POSTED;
    }

    public static function documentType(): string
    {
        return 'RF';
    }
}
