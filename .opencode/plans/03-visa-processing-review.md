# Visa Processing Review

## Scope
Complete visa lifecycle from application through approval/rejection. Includes `VisaService`, `VisaObserver`, `VisaResource`, and bulk operations.

## Areas to Cover
- `app/Models/Visa.php` — Status enum `VisaStatus`, dates, visa number, relationships
- `app/Services/VisaService.php` — `submitApplication()`, `markApproved()`, `markRejected()`, `bulkSubmitForTrip()`
- `app/Observers/VisaObserver.php` — Auto-sets `applied_at`/`approved_at` timestamps
- `app/Filament/Resources/VisaResource/` — List/View/Edit, actions, bulk operations, PDF export

## Review Prompts
1. **Status transition enforcement**: Can a visa go from `not_applied` directly to `approved`? Is the state machine properly enforced?
2. **Bulk operations**: `bulkSubmitForTrip()` — what happens if some visas are already submitted? Partial failures?
3. **Expired visas**: Is there a scheduled job to auto-expire visas that were `applied` too long ago?
4. **Visa auto-creation**: When BookingObserver creates a visa, does it handle the case where one already exists?
5. **Duplicate submissions**: Can the same visa be submitted twice?
6. **Validation**: Are required fields (passport number, nationality, etc.) validated before submission?
7. **Date integrity**: Are `applied_at` and `approved_at` dates consistent with status?
