# Observers, Events & Automations Review

## Scope
All 4 model observers, their side effects, data integrity guarantees, and missing automations.

## Areas to Cover
- `app/Observers/BookingObserver.php`
- `app/Observers/PackageObserver.php`
- `app/Observers/PaymentObserver.php`
- `app/Observers/VisaObserver.php`

## Review Prompts
1. **Transaction safety**: Are observer operations wrapped in the same transaction as the triggering save?
2. **Recursive loops**: Could any observer trigger another save that triggers another observer (infinite loop)?
3. **Ordering**: If both BookingObserver and PaymentObserver fire on related saves, is the sequence correct?
4. **Idempotency**: Are observer side effects idempotent? What happens if an observer runs twice?
5. **Edge cases in BookingObserver**: What if `total_seats` is null or zero? What if a booking is force-deleted?
6. **Edge cases in PaymentObserver**: What if `paid_amount` recalculation is triggered on refund before the refund is fully saved?
7. **Edge cases in VisaObserver**: What if status is set to `applied` but `applied_at` is already set?
8. **Missing automations**: Are there any business rules that should be automated via observers but aren't?
9. **Testing**: Are observers tested in isolation or only through integration tests?
