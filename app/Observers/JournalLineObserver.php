<?php

namespace App\Observers;

use App\Models\JournalLine;

class JournalLineObserver
{
    public function saving(JournalLine $line): void
    {
        if ($line->debit_amount < 0 || $line->credit_amount < 0) {
            throw new \RuntimeException('المبالغ يجب أن تكون قيماً موجبة.');
        }

        if ($line->debit_amount > 0 && $line->credit_amount > 0) {
            throw new \RuntimeException('لا يمكن أن يحتوي السطر على قيم مدين ودائن معاً.');
        }

        if ($line->debit_amount === 0 && $line->credit_amount === 0) {
            throw new \RuntimeException('يجب إدخال قيمة المدين أو الدائن.');
        }
    }
}
