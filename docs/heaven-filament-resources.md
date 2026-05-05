# HEAVEN – Filament Resources Plan

---

## 1. `ClientResource`

**Model:** `Client`

### Pages
| Page | Type | Notes |
|------|------|-------|
| `ListClients` | `ListRecords` | Default list with filters |
| `CreateClient` | `CreateRecord` | Default |
| `ViewClient` | `ViewRecord` | Infolist — primary page instead of edit |
| `EditClient` | `EditRecord` | Accessible from view page header |

### Relation Managers
| Name | Relation | Notes |
|------|----------|-------|
| `BookingsRelationManager` | `hasMany bookings` | Shows reference, package, status, net_price, remaining |
| `PaymentsRelationManager` | `hasManyThrough payments via bookings` | Read-only summary of all payments |

### Custom Actions
| Action | Scope | Description |
|--------|-------|-------------|
| `ExportClientCardAction` | Record (View page header) | Exports a printable PDF pilgrim card with passport, visa, booking details |
| `SendWhatsAppAction` | Record (Table row) | Opens WhatsApp deep-link to client's phone |
| `ViewStatementAction` | Record (View page header) | Opens `ClientStatementPage` (كشف حساب كامل) |

### Custom Pages
| Page | Route | Description |
|------|-------|-------------|
| `ClientStatementPage` | `/clients/{record}/statement` | Full payment & booking history per client — printable |

---

## 2. `PackageResource`

**Model:** `Package`

### Pages
| Page | Type | Notes |
|------|------|-------|
| `ListPackages` | `ListRecords` | Grouped/filtered by type and season_year |
| `CreatePackage` | `CreateRecord` | Default |
| `ViewPackage` | `ViewRecord` | Stats: total seats, reserved, available |
| `EditPackage` | `EditRecord` | Default |

### Relation Managers
| Name | Relation | Notes |
|------|----------|-------|
| `TripsRelationManager` | `hasMany trips` | Shows trip name, status, departure_at, seat count |
| `HotelsRelationManager` | `belongsToMany hotels` | Via `package_hotels` pivot — shows city, nights, cost |
| `BookingsRelationManager` | `hasMany bookings` | Read-only overview |

### Custom Actions
| Action | Scope | Description |
|--------|-------|-------------|
| `ToggleActiveAction` | Record (Table row) | Toggles `is_active` with confirmation |
| `DuplicatePackageAction` | Record (Table row) | Clones the package with a new season_year — resets reserved_seats to 0 |
| `ExportPackageSummaryAction` | Record (View page header) | Exports a PDF/Word package brochure summary |

---

## 3. `TripResource`

**Model:** `Trip`

### Pages
| Page | Type | Notes |
|------|------|-------|
| `ListTrips` | `ListRecords` | Filtered by status and package |
| `CreateTrip` | `CreateRecord` | Default |
| `ViewTrip` | `ViewRecord` | Dashboard: booked/available seats, visa stats, payment completion % |
| `EditTrip` | `EditRecord` | Default |

### Relation Managers
| Name | Relation | Notes |
|------|----------|-------|
| `BookingsRelationManager` | `hasMany bookings` | Full booking management scoped to this trip |
| `RoomsRelationManager` | `hasMany rooms` | Room inventory per trip |
| `ExpensesRelationManager` | `hasMany expenses` | Trip-level expenses |

### Custom Actions
| Action | Scope | Description |
|--------|-------|-------------|
| `DepartTripAction` | Record (View page header) | Sets status → `DEPARTED` with confirmation modal |
| `CompleteTripAction` | Record (View page header) | Sets status → `COMPLETED`, marks all bookings complete |
| `ExportManifestAction` | Record (View page header) | Exports airline manifest (client name, passport, seat) as Excel |
| `ExportRoomingListAction` | Record (View page header) | Exports hotel rooming list grouped by hotel → city → room |
| `BulkSubmitVisasAction` | Record (View page header) | Bulk-submits all pending visas for confirmed bookings in this trip |

### Custom Pages
| Page | Route | Description |
|------|-------|-------------|
| `TripDashboardPage` | `/trips/{record}/dashboard` | Live stats: seats, payments collected, visa statuses breakdown, room occupancy per hotel |
| `TripManifestPage` | `/trips/{record}/manifest` | Printable manifest — all confirmed pilgrims with passport/visa data |
| `TripRoomingPage` | `/trips/{record}/rooming` | Visual rooming list: hotel → room → assigned clients with drag-assign capability |

---

## 4. `BookingResource`

**Model:** `Booking`

### Pages
| Page | Type | Notes |
|------|------|-------|
| `ListBookings` | `ListRecords` | Filters: status, package, trip, date range |
| `CreateBooking` | `CreateRecord` | Wizard-style: Step 1 select client → Step 2 select package/trip → Step 3 pricing & room type |
| `ViewBooking` | `ViewRecord` | Full infolist: client info, package, pricing breakdown, payment timeline, visa status |
| `EditBooking` | `EditRecord` | Default |

### Relation Managers
| Name | Relation | Notes |
|------|----------|-------|
| `PaymentsRelationManager` | `hasMany payments` | Create, view, delete payments; shows running paid/remaining |
| `VisaRelationManager` | `hasOne visa` | Inline visa status management |

### Custom Actions
| Action | Scope | Description |
|--------|-------|-------------|
| `ConfirmBookingAction` | Record (View page header) | Sets status → `CONFIRMED` with seat validation |
| `CancelBookingAction` | Record (View page header) | Sets status → `CANCELLED` with reason modal, decrements seat count |
| `IssueRefundAction` | Record (View page header) | Opens modal to record a refund payment, sets status → `REFUNDED` |
| `AssignRoomAction` | Record (View page header) | Modal to pick an available room from the assigned trip |
| `PrintReceiptAction` | Record (Table row + View page) | Generates a printable payment receipt PDF |
| `PrintBookingVoucherAction` | Record (View page header) | Exports a pilgrim booking voucher with all trip details |
| `RecordPaymentAction` | Record (Table row) | Quick-pay modal without going into full edit |

---

## 5. `PaymentResource`

**Model:** `Payment`  
> Primarily managed via `BookingResource → PaymentsRelationManager`, but a standalone resource is needed for accounting overview.

### Pages
| Page | Type | Notes |
|------|------|-------|
| `ListPayments` | `ListRecords` | Full ledger view — filters: type, method, date range, booking, received_by |
| `CreatePayment` | `CreateRecord` | Must select booking first — rare standalone use |
| `ViewPayment` | `ViewRecord` | Default |

### Custom Actions
| Action | Scope | Description |
|--------|-------|-------------|
| `PrintReceiptAction` | Record (Table row) | Generates individual receipt PDF |
| `ExportLedgerAction` | Page (List header) | Exports filtered payments as Excel ledger |

### Custom Pages
| Page | Route | Description |
|------|-------|-------------|
| `RevenueReportPage` | `/payments/revenue-report` | Chart + table: revenue by package, trip, month; collected vs outstanding |

---

## 6. `VisaResource`

**Model:** `Visa`  
> Primarily managed through `BookingResource`, but a standalone resource is needed for bulk visa operations.

### Pages
| Page | Type | Notes |
|------|------|-------|
| `ListVisas` | `ListRecords` | Filters: status, trip, booking reference |
| `ViewVisa` | `ViewRecord` | Shows client passport details alongside visa record |
| `EditVisa` | `EditRecord` | Default |

### Custom Actions
| Action | Scope | Description |
|--------|-------|-------------|
| `MarkApprovedAction` | Record (Table row) | Modal: enter visa number + expiry → sets status `APPROVED` |
| `MarkRejectedAction` | Record (Table row) | Modal: enter rejection reason → sets status `REJECTED` |
| `BulkMarkApprovedAction` | Bulk (Table) | Bulk approval with shared visa number prefix |
| `BulkSubmitAction` | Bulk (Table) | Bulk-sets status → `APPLIED` for selected records |
| `ExportVisaListAction` | Page (List header) | Exports visa list with passport data as Excel for embassy submission |

---

## 7. `RoomResource`

**Model:** `Room`  
> Primarily managed through `TripResource → RoomsRelationManager`, but standalone resource for room-level management.

### Pages
| Page | Type | Notes |
|------|------|-------|
| `ListRooms` | `ListRecords` | Filtered by trip and hotel; shows occupancy badge |
| `CreateRoom` | `CreateRecord` | Default |
| `ViewRoom` | `ViewRecord` | Shows assigned bookings/clients in this room |
| `EditRoom` | `EditRecord` | Default |

### Relation Managers
| Name | Relation | Notes |
|------|----------|-------|
| `BookingsRelationManager` | `hasMany bookings` | Clients assigned to this room — attach/detach |

### Custom Actions
| Action | Scope | Description |
|--------|-------|-------------|
| `AssignClientAction` | Record (View page header) | Select an unassigned booking from the same trip to add to this room |
| `UnassignClientAction` | Relation Manager row | Detaches client from room, decrements occupied count |

---

## 8. `HotelResource`

**Model:** `Hotel`

### Pages
| Page | Type | Notes |
|------|------|-------|
| `ListHotels` | `ListRecords` | Filtered by city (Makkah / Madinah) and star rating |
| `CreateHotel` | `CreateRecord` | Default |
| `ViewHotel` | `ViewRecord` | Default |
| `EditHotel` | `EditRecord` | Default |

### Relation Managers
| Name | Relation | Notes |
|------|----------|-------|
| `PackagesRelationManager` | `belongsToMany packages` | Shows which packages use this hotel |
| `RoomsRelationManager` | `hasMany rooms` | Rooms managed per hotel |

---

## 9. `SupplierResource`

**Model:** `Supplier`

### Pages
| Page | Type | Notes |
|------|------|-------|
| `ListSuppliers` | `ListRecords` | Filtered by type |
| `CreateSupplier` | `CreateRecord` | Default |
| `ViewSupplier` | `ViewRecord` | Default |
| `EditSupplier` | `EditRecord` | Default |

### Relation Managers
| Name | Relation | Notes |
|------|----------|-------|
| `HotelsRelationManager` | `hasMany hotels` | Hotels owned by this supplier (if supplier is a hotel chain) |

---

## 10. `EmployeeResource`

**Model:** `Employee`

### Pages
| Page | Type | Notes |
|------|------|-------|
| `ListEmployees` | `ListRecords` | Filtered by role and active status |
| `CreateEmployee` | `CreateRecord` | Default |
| `ViewEmployee` | `ViewRecord` | Infolist with salary info and linked user account |
| `EditEmployee` | `EditRecord` | Default |

### Custom Actions
| Action | Scope | Description |
|--------|-------|-------------|
| `LinkUserAccountAction` | Record (View page header) | Modal to link or create a `User` account for this employee |
| `DeactivateEmployeeAction` | Record (Table row) | Sets `is_active = false` + `left_at = today` with confirmation |

---

## 11. `ExpenseResource`

**Model:** `Expense`

### Pages
| Page | Type | Notes |
|------|------|-------|
| `ListExpenses` | `ListRecords` | Filters: category, trip, date range, paid_by |
| `CreateExpense` | `CreateRecord` | Default |
| `ViewExpense` | `ViewRecord` | Default |
| `EditExpense` | `EditRecord` | Default |

### Custom Actions
| Action | Scope | Description |
|--------|-------|-------------|
| `ExportExpensesAction` | Page (List header) | Exports filtered expenses as Excel |

---

## Filament Widgets (Dashboard)

| Widget | Type | Description |
|--------|------|-------------|
| `BookingsOverviewWidget` | `StatsOverviewWidget` | Total bookings today / this month / pending |
| `RevenueWidget` | `StatsOverviewWidget` | Total collected, outstanding, refunded |
| `VisaStatusWidget` | `StatsOverviewWidget` | Count per VisaStatus across active trips |
| `UpcomingTripsWidget` | `TableWidget` | Next 5 departures with seat fill % |
| `RecentBookingsWidget` | `TableWidget` | Last 10 bookings with status badge |
| `RevenueChartWidget` | `ChartWidget` | Monthly revenue bar chart (current year) |
| `SeatOccupancyWidget` | `ChartWidget` | Donut: confirmed vs available seats per active package |

---

## Navigation Groups

```
حجاج وحجوزات
  ├── Clients (ClientResource)
  ├── Bookings (BookingResource)
  └── Payments (PaymentResource)

الرحلات والباقات
  ├── Packages (PackageResource)
  ├── Trips (TripResource)
  └── Rooms (RoomResource)

التأشيرات
  └── Visas (VisaResource)

الموردون والفنادق
  ├── Suppliers (SupplierResource)
  └── Hotels (HotelResource)

الموارد البشرية
  ├── Employees (EmployeeResource)
  └── Expenses (ExpenseResource)

التقارير
  ├── Revenue Report (RevenueReportPage — custom page under PaymentResource)
  └── Trip Dashboard (TripDashboardPage — custom page under TripResource)
```
