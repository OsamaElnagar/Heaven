# HEAVEN — Review Topics

## 1. Clients
- `app/Models/Client.php`
- `app/Filament/Resources/Clients/ClientResource.php`
- `app/Filament/Resources/Clients/Schemas/ClientForm.php`
- `app/Filament/Resources/Clients/Pages/ClientAccountingStatementPage.php`

> Review the clients module — the core entity representing Hajj & Umrah pilgrims with personal data, passports, mahram info, and their accounting statements.

## 2. Packages
- `app/Models/Package.php`
- `app/Filament/Resources/Packages/PackageResource.php`
- `app/Filament/Resources/Packages/Schemas/PackageForm.php`
- `app/Filament/Resources/Packages/Widgets/SeatOccupancyWidget.php`

> Review the packages module — Hajj & Umrah travel packages with grades, pricing, seat capacity, and linked hotels.

## 3. Trips
- `app/Models/Trip.php`
- `app/Filament/Resources/Trips/TripResource.php`
- `app/Services/TripService.php`
- `app/Filament/Resources/Trips/Pages/TripDashboardPage.php`

> Review the trips module — concrete departures of packages with flight details, status lifecycle, rooming, manifest, and trip-level operations.

## 4. Bookings
- `app/Models/Booking.php`
- `app/Filament/Resources/Bookings/BookingResource.php`
- `app/Services/BookingService.php`
- `app/Filament/Resources/Bookings/Schemas/BookingForm.php`

> Review the bookings module — the full booking lifecycle from inquiry to completion, linking clients to packages/trips with room assignment, pricing, and payment tracking.

## 5. Visa
- `app/Models/Visa.php`
- `app/Filament/Resources/Visas/VisaResource.php`
- `app/Services/VisaService.php`
- `app/Filament/Resources/Visas/Schemas/VisaForm.php`

> Review the visa module — visa status tracking per pilgrim with workflow states and bulk embassy submission support.

## 6. Hotels & Rooms
- `app/Models/Hotel.php`
- `app/Models/Room.php`
- `app/Filament/Resources/Hotels/HotelResource.php`
- `app/Filament/Resources/Rooms/RoomResource.php`

> Review the hotels & rooms module — hotel properties with supplier ownership and room assignments within trips for client placement.

## 7. Suppliers
- `app/Models/Supplier.php`
- `app/Filament/Resources/Suppliers/SupplierResource.php`
- `app/Filament/Resources/Suppliers/Pages/SupplierAccountingStatementPage.php`

> Review the suppliers module — external vendors (hotels, airlines, transport, catering) with accounting integration.

## 8. Employees
- `app/Models/Employee.php`
- `app/Filament/Resources/Employees/EmployeeResource.php`
- `app/Filament/Resources/Employees/Pages/EmployeeAccountingStatementPage.php`

> Review the employees module — staff management with salary types, role assignment, user account linking, and accounting integration.

## 9. Payments & Vouchers
- `app/Models/ReceiptVoucher.php`
- `app/Models/PaymentVoucher.php`
- `app/Models/RefundVoucher.php`
- `app/Filament/Resources/ReceiptVouchers/ReceiptVoucherResource.php`
- `app/Filament/Resources/PaymentVouchers/PaymentVoucherResource.php`
- `app/Filament/Resources/RefundVouchers/RefundVoucherResource.php`

> Review the payments & vouchers module — three types of financial vouchers (receipt, payment, refund) that generate double-entry journal entries when posted.

## 10. Expenses
- `app/Models/Expense.php`
- `app/Filament/Resources/Expenses/ExpenseResource.php`
- `app/Filament/Resources/Expenses/Schemas/ExpenseForm.php`

> Review the expenses module — trip-level expense tracking with categories, payment methods, receipt attachments, and accounting integration.

## 11. Chart of Accounts
- `app/Models/Account.php`
- `app/Filament/Resources/Accounts/AccountResource.php`
- `app/Services/Accounting/AccountService.php`
- `app/Services/Accounting/AccountAutoCreateService.php`

> Review the chart of accounts — the foundation of the double-entry accounting system with hierarchical accounts and automatic party account creation.

## 12. Journal Entries & General Ledger
- `app/Models/JournalEntry.php`
- `app/Models/JournalLine.php`
- `app/Services/Accounting/JournalEntryService.php`
- `app/Filament/Resources/JournalEntries/JournalEntryResource.php`
- `app/Filament/Resources/JournalEntries/Pages/TrialBalance.php`
- `app/Filament/Resources/JournalEntries/Pages/GeneralLedger.php`

> Review the journal entries & general ledger — the heart of the accounting system with draft-to-posted workflow, reversal support, trial balance, and general ledger reports.

## 13. Fiscal Years & Document Sequences
- `app/Models/FiscalYear.php`
- `app/Models/DocumentSequence.php`
- `app/Services/Accounting/FiscalYearService.php`
- `app/Services/Accounting/DocumentSequenceService.php`
- `app/Filament/Resources/FiscalYears/FiscalYearResource.php`

> Review fiscal years & document sequences — fiscal year open/close lifecycle, opening balances, and auto-incrementing document numbering across all voucher types.

## 14. Safes & Bank Accounts
- `app/Models/Safe.php`
- `app/Models/BankAccount.php`
- `app/Filament/Resources/Safes/SafeResource.php`
- `app/Filament/Resources/BankAccounts/BankAccountResource.php`

> Review safes & bank accounts — physical cash safes and bank accounts linked to the Chart of Accounts and responsible employees.

## 15. Reporting
- `app/Filament/Pages/Reports/BalanceSheet.php`
- `app/Filament/Pages/Reports/IncomeStatement.php`
- `app/Filament/Pages/Reports/CashFlowStatement.php`
- `app/Filament/Pages/Reports/ClientAgingReport.php`
- `app/Filament/Pages/Reports/PackageProfitabilityReport.php`
- `app/Services/PdfService.php`

> Review reporting — financial reports (Balance Sheet, Income Statement, Cash Flow, Client Aging, Expense Summary, Package Profitability, Supplier Balances) powered by journal entry data.

## 16. Public Website (Client Portal)
- `routes/web.php`
- `app/Models/Post.php`
- `app/Models/Faq.php`
- `app/Models/GalleryItem.php`
- `app/Filament/Resources/GalleryItems/GalleryItemResource.php`

> Review the public-facing website — lightweight Livewire pages for package listings, booking inquiry, booking tracker, news, FAQ, and gallery.

## 17. Dashboard & Widgets
- `app/Filament/Widgets/RecentBookingsWidget.php`
- `app/Filament/Widgets/RevenueChartWidget.php`
- `app/Filament/Widgets/UpcomingTripsWidget.php`

> Review dashboard & widgets — admin dashboard widgets showing key business metrics like recent bookings, revenue, and upcoming trips.

## 18. Auth & User Management
- `app/Models/User.php`
- `app/Actions/Fortify/CreateNewUser.php`
- `config/fortify.php`

> Review auth & user management — Fortify-based authentication with two-factor auth, profile management, and password reset.

## 19. Cross-Cutting: Traits & Services
- `app/Models/Concerns/HasEntityCode.php`
- `app/Models/Concerns/HasDocumentNumber.php`
- `app/Filament/Components/Filters/DateRangeFilter.php`
- `app/Support/Statement/PartyStatementPage.php`

> Review cross-cutting concerns — shared traits, reusable components, and base classes used across multiple modules.
