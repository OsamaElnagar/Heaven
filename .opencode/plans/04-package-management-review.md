# Package Management Review

## Scope
Package creation, seat tracking, grades, pricing, hotel assignments, and public display. Includes `Package`, `PackageObserver`, `PackageResource`, and all public-facing package pages.

## Areas to Cover
- `app/Models/Package.php` — Type (Hajj/Umrah), Grade (Economy/Standard/VIP/VVIP), seat tracking, SoftDeletes, route key = slug
- `app/Models/PackageHotel.php` — Pivot: city, nights, cost_per_person
- `app/Observers/PackageObserver.php` — Clamps `reserved_seats` to `total_seats`, auto-deactivates past Hajj packages
- `app/Filament/Resources/PackageResource/` — List with stats, Duplicate action, Toggle Active, PDF export
- `resources/views/pages/facing/packages*.blade.php` — All public package views

## Review Prompts
1. **Seat overselling**: When `reserved_seats` is clamped to `total_seats`, does this silently allow overselling? Should it throw an error instead?
2. **Date-based deactivation**: Does it correctly handle Umrah packages that run year-round?
3. **Pricing consistency**: Is package price in sync with hotel costs + markups? Does `cost_per_person` factor in correctly?
4. **Duplicate slugs**: Can two packages have the same slug? Is there a unique index?
5. **Soft delete cascade**: When a package is deleted, do related bookings/trips/hotels handle it gracefully?
6. **Grade filters**: Are VIP/featured/group frontend filters working correctly?
7. **Seat availability**: Does the public UI show real-time remaining seats or stale data?
8. **Package duplication**: Does the Duplicate action correctly copy all related hotels and pricing?
