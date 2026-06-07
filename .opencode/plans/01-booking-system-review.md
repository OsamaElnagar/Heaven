# Booking System Review

## Scope
The booking lifecycle — from creation through confirmation, room assignment, cancellation, and refund. Includes `BookingService`, `BookingObserver`, `BookingResource` (Filament), and the public booking form.

## Areas to Cover
- `app/Models/Booking.php` — Model, status enum `BookingStatus`, pricing fields, relationships
- `app/Services/BookingService.php` — `createBooking()`, `cancelBooking()`, `assignRoom()`, `calculatePricing()`
- `app/Observers/BookingObserver.php` — Auto-generates reference (BK-YEAR-XXXXX), calculates net price, manages reserved_seats, auto-creates Visa on confirmation
- `app/Filament/Resources/BookingResource/` — CRUD, actions (Confirm, Cancel, Assign Room, Record Payment, Issue Refund, Print Receipt, Print Booking Voucher)
- `resources/views/pages/facing/book.blade.php` — Public booking form
- `resources/views/pages/facing/track.blade.php` — Booking tracking page
- `database/migrations/*_create_bookings_table.php`

## Review Prompts
1. **Race conditions**: When multiple users book the last seat on a package simultaneously, does `reserved_seats` handling have race conditions? Should we use database locks or queue?
2. **Pricing calculation**: Is `calculatePricing()` correct for all room types and package grades? Are surcharges applied properly?
3. **Reference generation**: Is the `BK-YEAR-XXXXX` pattern unique under high concurrency? Could two bookings get the same reference?
4. **State machine gaps**: Are transitions (pending→confirmed→cancelled→refunded) properly enforced? Can a booking be cancelled after it's already refunded?
5. **Visa auto-creation**: When a booking is confirmed and auto-creates a Visa, what happens if the client already has a visa for that trip?
6. **Soft delete implications**: Booking uses SoftDeletes — do all queries and relationships properly exclude trashed records? What happens to payments/visas when a booking is soft-deleted?
7. **Room assignment validation**: Can a room be over-assigned (more occupants than capacity)? Is double-booking prevented?
8. **Public booking form**: Is the booking form properly validated? Are prices exposed correctly? Is there CSRF protection?
9. **Notification gaps**: Are customers notified on booking confirmation, cancellation, or status changes?
