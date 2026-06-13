<?php

namespace App\Observers;

use App\Models\PaymentVoucher;

class PaymentVoucherObserver
{
    public function saving(PaymentVoucher $voucher): void
    {
        $voucher->net_amount = (int) $voucher->amount - (int) $voucher->withholding_amount;

        $payee = $voucher->payee_type?->value;
        $voucher->supplier_id = $payee === 'supplier' ? $voucher->supplier_id : null;
        $voucher->client_id = $payee === 'client' ? $voucher->client_id : null;
        $voucher->employee_id = $payee === 'employee' ? $voucher->employee_id : null;
        $voucher->branch_id = $payee === 'branch' ? $voucher->branch_id : null;
        $voucher->agent_id = $payee === 'agent' ? $voucher->agent_id : null;
    }
}
