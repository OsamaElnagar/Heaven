# Payment System Review

## Scope
Payment recording, installment tracking, refunds, and auto-status recalculation. Includes `PaymentService`, `PaymentObserver`, `PaymentResource`, and public payment interactions.

## Areas to Cover
- `app/Models/Payment.php` — Types (deposit/installment/final/refund), methods (cash/bank/instapay/check)
- `app/Services/PaymentService.php` — `recordPayment()`, `issueRefund()`, `getPaymentSummary()`
- `app/Observers/PaymentObserver.php` — Recalculates `booking.paid_amount`, auto-updates booking status
- `app/Filament/Resources/PaymentResource/` — CRUD, Revenue Report page

## Review Prompts
1. **Payment accuracy**: When recording an installment, does `paid_amount` correctly sum all non-refund payments? Does it properly subtract refunds?
2. **Status auto-update**: When does a booking auto-transition from PENDING to CONFIRMED? Is the threshold correct?
3. **Refund integrity**: Can a refund exceed total paid amount? Is negative balance prevented?
4. **Check payments**: How are check/bounced-check scenarios handled? Is there a reconciliation flow?
5. **Race conditions**: Concurrent payment recordings for the same booking — are there race conditions in the observer's `paid_amount` recalculation?
6. **Audit trail**: Is there proper audit trail for who recorded/refunded each payment and when?
7. **Reporting accuracy**: Does `getPaymentSummary()` match the revenue reports?
8. **Missing payment types**: Are there any missing payment types or methods needed for this business?
