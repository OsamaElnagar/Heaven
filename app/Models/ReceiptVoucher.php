<?php

namespace App\Models;

use App\Enums\ExpenseStatus;
use App\Enums\PayerType;
use App\Enums\PaymentType;
use App\Enums\VoucherPaymentMethod;
use App\Models\Concerns\HasDocumentNumber;
use App\Observers\ReceiptVoucherObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy([ReceiptVoucherObserver::class])]
class ReceiptVoucher extends Model
{
    use HasDocumentNumber;
    use SoftDeletes;

    protected $fillable = [
        'number',
        'voucher_date',
        'receipt_method',
        'safe_id',
        'bank_account_id',
        'cheque_number',
        'cheque_date',
        'amount',
        'payment_type',
        'payer_type',
        'client_id',
        'booking_id',
        'supplier_id',
        'employee_id',
        'payer_name',
        'description',
        'reference',
        'attachment',
        'status',
        'journal_entry_id',
        'created_by',
        'posted_by',
        'posted_at',
    ];

    protected $casts = [
        'receipt_method' => VoucherPaymentMethod::class,
        'payer_type' => PayerType::class,
        'payment_type' => PaymentType::class,
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

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
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

    public function isPosted(): bool
    {
        return $this->status === ExpenseStatus::POSTED;
    }

    public function payerLabel(): string
    {
        return match ($this->payer_type) {
            PayerType::CLIENT => $this->client?->name ?? 'عميل',
            PayerType::SUPPLIER => $this->supplier?->name ?? 'مورد',
            PayerType::EMPLOYEE => $this->employee?->name ?? 'موظف',
            PayerType::OTHER => $this->payer_name ?? 'آخر',
            default => '-',
        };
    }

    public static function documentType(): string
    {
        return 'RV';
    }
}
