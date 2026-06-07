<?php

namespace App\Models;

use App\Enums\ExpenseStatus;
use App\Enums\PayeeType;
use App\Enums\VoucherPaymentMethod;
use App\Models\Concerns\HasDocumentNumber;
use App\Observers\PaymentVoucherObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy([PaymentVoucherObserver::class])]
class PaymentVoucher extends Model
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
        'withholding_amount',
        'net_amount',
        'payee_type',
        'client_id',
        'supplier_id',
        'employee_id',
        'payee_name',
        'expense_id',
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
        'payment_method' => VoucherPaymentMethod::class,
        'payee_type' => PayeeType::class,
        'status' => ExpenseStatus::class,
        'voucher_date' => 'date',
        'cheque_date' => 'date',
        'amount' => 'integer',
        'withholding_amount' => 'integer',
        'net_amount' => 'integer',
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

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
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

    public function payeeLabel(): string
    {
        return match ($this->payee_type) {
            PayeeType::CLIENT => $this->client?->name ?? 'عميل',
            PayeeType::SUPPLIER => $this->supplier?->name ?? 'مورد',
            PayeeType::EMPLOYEE => $this->employee?->name ?? 'موظف',
            PayeeType::OTHER => $this->payee_name ?? 'آخر',
            default => '-',
        };
    }

    public static function documentType(): string
    {
        return 'PV';
    }
}
