# Trip Management Review

## Scope
Trip logistics — flights, manifests, rooming lists, departure/complete workflows. Includes `TripService`, `TripResource`, and related pages.

## Areas to Cover
- `app/Models/Trip.php` — Flight details, dates, status enum `TripStatus`, relationships
- `app/Services/TripService.php` — `depart()`, `complete()`, `getRoomingList()`, `getManifest()`
- `app/Filament/Resources/TripResource/` — CRUD, Dashboard/Manifest/Rooming pages, RelationManagers

## Review Prompts
1. **State machine**: Can a trip go from `upcoming` directly to `completed` without `departed`? Are transitions enforced?
2. **Manifest accuracy**: Does `getManifest()` include all confirmed bookings only? Exclude cancelled?
3. **Rooming list integrity**: Does `getRoomingList()` correctly account for capacity and gender segregation?
4. **Departure validation**: Are there checks before marking departed (all visas approved, rooms assigned)?
5. **Date logic**: Are departure/return dates validated (departure before return)?
6. **Related data cleanup**: When a trip is cancelled, what happens to its bookings, rooms, expenses?
