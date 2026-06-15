Complete Filament Resources Overview
Navigation Groups (from AdminPanelProvider.php)
The panel defines 9 navigation groups in Arabic:

# Group Label (Arabic) Translation

1 عملاء وحجوزات Customers & Bookings
2 الموردون والفنادق والرحلات Suppliers, Hotels & Trips
3 الوكلاء والفروع Agents & Branches
4 الموارد البشرية Human Resources
5 المحتوى Content
6 المحاسبة Accounting
7 الخزينة والسندات Treasury & Vouchers
8 التقارير المالية Financial Reports
9 الإعدادات Settings
Group 1: عملاء وحجوزات (Customers & Bookings)

1. ClientResource
   File: F:\Herd\Heaven\app\Filament\Resources\Clients\ClientResource.php
   Model: App\Models\Client
   Navigation: Group 'عملاء وحجوزات', Label 'العملاء', Icon: Heroicon::OutlinedUsers
   Labels: modelLabel = 'عميل', pluralModelLabel = 'العملاء', recordTitleAttribute = 'name'
   Pages: index (ListClients), create (CreateClient), view (ViewClient), edit (EditClient), statement (ClientStatementPage), accounting-statement (ClientAccountingStatementPage)
   Global Search: name, name_en, national_id, passport_number, phone, email
   Form Sections/Fields (ClientForm): Section 'البيانات الشخصية': name, name_en, national_id, passport_number, passport_expiry, date_of_birth, gender (Gender enum), marital_status (MaritalStatus enum), blood_type, governorate. Section 'معلومات الاتصال': phone, phone_alt, email, address. Section 'بيانات المحرم': mahram_name, mahram_relation, mahram_phone. Section 'ملاحظات طبية': medical_notes.
   Table Columns (ClientsTable): name, national_id, passport_number, phone, governorate, gender, passport_expiry, created_at
   Table Actions: ViewAction, EditAction, SendWhatsAppAction
   Relation Managers: BookingsRelationManager, PaymentsRelationManager
   Custom Actions: ViewStatementAction, SendWhatsAppAction, ExportClientCardAction
2. BookingResource
   File: F:\Herd\Heaven\app\Filament\Resources\Bookings\BookingResource.php
   Model: App\Models\Booking
   Navigation: Group 'عملاء وحجوزات', Label 'الحجوزات', Icon: Heroicon::OutlinedTicket
   Labels: modelLabel = 'حجز', pluralModelLabel = 'الحجوزات', recordTitleAttribute = 'reference'
   Pages: index (ListBookings), create (CreateBooking), view (ViewBooking), edit (EditBooking)
   Global Search: reference
   Form Sections/Fields (BookingForm): Section 'بيانات الحجز': client_id (belongs to Client), package_id (belongs to Package), trip_id (belongs to Trip), room_type (RoomType enum), channel (BookingChannel enum), branch_id (conditional), agent_id (conditional), status (BookingStatus enum). Section 'التسعير': total_price, discount, net_price, paid_amount. Section 'معلومات إضافية': room_id, due_date, notes.
   Includes dynamic pricing calculation via BookingService::calculatePricing()
   Table Columns (BookingsTable): reference, client.name, package.name, channel, branch.name, agent.name, status, net_price, paid_amount, remaining, due_date, created_at
   Infolist (BookingInfolist): Sections for 'معلومات الحجز', 'التسعير', 'معلومات إضافية'
   Table Actions: ViewAction, EditAction, RecordPaymentAction, PrintReceiptAction
   Relation Managers: PaymentsRelationManager, VisaRelationManager
   Custom Actions: RecordPaymentAction, PrintReceiptAction, PrintBookingVoucherAction, IssueRefundAction, ConfirmBookingAction, CancelBookingAction, AssignRoomAction
   Widgets: BookingsStatsWidget
3. VisaResource
   File: F:\Herd\Heaven\app\Filament\Resources\Visas\VisaResource.php
   Model: App\Models\Visa
   Navigation: Group 'عملاء وحجوزات', Label 'التأشيرات', Icon: Heroicon::OutlinedDocumentCheck
   Labels: modelLabel = 'تأشيرة', pluralModelLabel = 'التأشيرات', recordTitleAttribute = 'visa_number'
   Pages: index (ListVisas), create (CreateVisa), view (ViewVisa), edit (EditVisa)
   Form Sections/Fields (VisaForm): Section 'بيانات التأشيرة': booking_id, status (VisaStatus enum), visa_number. Section 'التواريخ': applied_at, approved_at, expiry_date. Section 'ملاحظات': rejection_reason, notes.
   Table Columns (VisasTable): booking.reference, booking.client.name, status, visa_number, applied_at, approved_at, expiry_date
   Table Actions: ViewAction, EditAction, MarkApprovedAction, MarkRejectedAction
   Custom Actions: MarkRejectedAction, MarkApprovedAction, ExportVisaListAction, BulkSubmitAction, BulkMarkApprovedAction
   Widgets: VisaStatusWidget
   Group 2: الموردون والفنادق والرحلات (Suppliers, Hotels & Trips)
4. PackageResource (Sort: 0)
   File: F:\Herd\Heaven\app\Filament\Resources\Packages\PackageResource.php
   Model: App\Models\Package
   Navigation: Group 'الموردون والفنادق والرحلات', Label 'الباقات', Icon: Heroicon::OutlinedGift, Sort: 0
   Labels: modelLabel = 'باقة', pluralModelLabel = 'الباقات', recordTitleAttribute = 'name'
   Pages: index (ListPackages), create (CreatePackage), view (ViewPackage), edit (EditPackage)
   Global Search: name
   Form Sections/Fields (PackageForm): Section 'بيانات الباقة': name, type_id (PackageType), grade (PackageGrade enum), season_year, duration_nights, base_price. Section 'المقاعد والتواريخ': departure_date, return_date, total_seats, reserved_seats, available_seats, is_active, front_office_visible. Section 'تفاصيل إضافية': includes, excludes, notes.
   Table Columns (PackagesTable): name, type.name_ar, grade, season_year, base_price, total_seats, reserved_seats, is_active, departure_date
   Table Actions: ToggleActiveAction, ViewAction, EditAction
   Relation Managers: TripsRelationManager, BookingsRelationManager
   Custom Actions: ExportPackageSummaryAction, ToggleActiveAction
   Widgets: SeatOccupancyWidget, PackagesStatsWidget
5. TripResource (Sort: 1)
   File: F:\Herd\Heaven\app\Filament\Resources\Trips\TripResource.php
   Model: App\Models\Trip
   Navigation: Group 'الموردون والفنادق والرحلات', Label 'الرحلات', Icon: Heroicon::OutlinedGlobeAlt, Sort: 1
   Labels: modelLabel = 'رحلة', pluralModelLabel = 'الرحلات', recordTitleAttribute = 'name'
   Pages: index (ListTrips), create (CreateTrip), view (ViewTrip), edit (EditTrip), dashboard (TripDashboardPage), manifest (TripManifestPage), rooming (TripRoomingPage)
   Global Search: name, flight_number
   Form Sections/Fields (TripForm): Section 'بيانات الرحلة': name, package_id, status (TripStatus enum), airline, flight_number. Section 'مواعيد السفر': departure_at, return_at, departure_airport. Section 'ملاحظات': notes.
   Table Columns (TripsTable): name, package.name, status, departure_at, airline, departure_airport, flight_number, return_at
   Table Actions: ViewAction, EditAction
   Relation Managers: BookingsRelationManager, RoomsRelationManager, ExpensesRelationManager
   Custom Actions: BulkSubmitVisasAction, CompleteTripAction, ExportRoomingListAction, ExportManifestAction, DepartTripAction
6. SupplierResource (Sort: 2)
   File: F:\Herd\Heaven\app\Filament\Resources\Suppliers\SupplierResource.php
   Model: App\Models\Supplier
   Navigation: Group 'الموردون والفنادق والرحلات', Label 'الموردون', Icon: Heroicon::OutlinedTruck, Sort: 2
   Labels: modelLabel = 'مورد', pluralModelLabel = 'الموردون', recordTitleAttribute = 'name'
   Pages: index (ListSuppliers), create (CreateSupplier), view (ViewSupplier), edit (EditSupplier), accounting-statement (SupplierAccountingStatementPage)
   Global Search: code, name, phone, email
   Form Sections/Fields (SupplierForm): Section 'بيانات المورد': name, type (SupplierType enum), country, city. Section 'معلومات الاتصال': contact_person, phone, email. Section 'ملاحظات': notes.
   Table Columns (SuppliersTable): name, type, country, city, contact_person, phone, email
   Table Actions: ViewAction, EditAction
   Relation Managers: HotelsRelationManager
7. HotelResource (Sort: 3)
   File: F:\Herd\Heaven\app\Filament\Resources\Hotels\HotelResource.php
   Model: App\Models\Hotel
   Navigation: Group 'الموردون والفنادق والرحلات', Label 'الفنادق', Icon: Heroicon::OutlinedBuildingOffice, Sort: 3
   Labels: modelLabel = 'فندق', pluralModelLabel = 'الفنادق', recordTitleAttribute = 'name'
   Pages: index (ListHotels), create (CreateHotel), view (ViewHotel), edit (EditHotel)
   Global Search: name
   Form Sections/Fields (HotelForm): Section 'بيانات الفندق': name, supplier_id, city_id, stars, distance_to_haram. Section 'ملاحظات': notes.
   Table Columns (HotelsTable): name, city.name_ar, stars, supplier.name, distance_to_haram
   Table Actions: ViewAction, EditAction
   Relation Managers: PackagesRelationManager, RoomsRelationManager
8. RoomResource (Sort: 4)
   File: F:\Herd\Heaven\app\Filament\Resources\Rooms\RoomResource.php
   Model: App\Models\Room
   Navigation: Group 'الموردون والفنادق والرحلات', Label 'الغرف', Icon: Heroicon::OutlinedHomeModern, Sort: 4
   Labels: modelLabel = 'غرفة', pluralModelLabel = 'الغرف', recordTitleAttribute = 'room_number'
   Pages: index (ListRooms), create (CreateRoom), view (ViewRoom), edit (EditRoom)
   Form Sections/Fields (RoomForm): Section 'بيانات الغرفة': hotel_id, trip_id, room_number, type (RoomType enum with auto-capacity), capacity, occupied, available, price_per_person. Section 'ملاحظات': notes.
   Table Columns (RoomsTable): room_number, hotel.name, trip.name, type, capacity, occupied
   Table Actions: ViewAction, EditAction
   Relation Managers: BookingsRelationManager
   Custom Actions: AssignClientAction, UnassignClientAction
   Group 3: الوكلاء والفروع (Agents & Branches)
9. BranchResource
   File: F:\Herd\Heaven\app\Filament\Resources\Branches\BranchResource.php
   Model: App\Models\Branch
   Navigation: Group 'الوكلاء والفروع', Label 'الفروع', Icon: Heroicon::OutlinedBuildingStorefront
   Labels: modelLabel = 'فرع', pluralModelLabel = 'الفروع', recordTitleAttribute = 'name'
   Pages: index (ListBranches), create (CreateBranch), edit (EditBranch)
   Form Sections/Fields (BranchForm): Section 'بيانات الفرع': code, name, phone, email, city_id, address. Section 'معلومات الإدارة والعمولات': manager_name, manager_phone, commission_percentage, is_active.
   Table Columns (BranchesTable): code, name, phone, email, city.name, manager_name, manager_phone, commission_percentage, is_active, created_at
   Table Actions: EditAction
10. AgentResource
    File: F:\Herd\Heaven\app\Filament\Resources\Agents\AgentResource.php
    Model: App\Models\Agent
    Navigation: Group 'الوكلاء والفروع', Label 'الوكلاء/المندوبون', Icon: Heroicon::OutlinedUser
    Labels: modelLabel = 'وكيل/مندوب', pluralModelLabel = 'وكلاء/مندوبون', recordTitleAttribute = 'name'
    Pages: index (ListAgents), create (CreateAgent), edit (EditAgent)
    Form Sections/Fields (AgentForm): Section 'بيانات الوكيل': code, name, phone, email, national_id. Section 'العمولة والعقد': commission_percentage, contract_date, notes, is_active.
    Table Columns (AgentsTable): code, name, phone, email, national_id, commission_percentage, contract_date, is_active, created_at
    Table Actions: EditAction
11. CommissionResource
    File: F:\Herd\Heaven\app\Filament\Resources\Commissions\CommissionResource.php
    Model: App\Models\Commission
    Navigation: Group 'الوكلاء والفروع', Label 'العمولات', Icon: Heroicon::OutlinedCurrencyDollar
    Labels: modelLabel = 'عمولة', pluralModelLabel = 'العمولات', recordTitleAttribute = 'id'
    Pages: index (ListCommissions), create (CreateCommission), edit (EditCommission)
    Form Sections/Fields (CommissionForm): Section 'بيانات العمولة': booking_id, branch_id, agent_id, commission_type (CommissionType enum), commission_rate, amount, status (CommissionStatus enum), paid_at, payment_voucher_id, notes.
    Table Columns (CommissionsTable): booking.reference, branch.name, agent.name, commission_type, commission_rate, amount, status, paid_at, paymentVoucher.number, created_at
    Table Actions: EditAction
    Group 4: الموارد البشرية (Human Resources)
12. AttendanceResource (Sort: 1)
    File: F:\Herd\Heaven\app\Filament\Resources\Attendances\AttendanceResource.php
    Model: App\Models\Attendance
    Navigation: Group 'الموارد البشرية', Label 'الحضور والانصراف', Icon: Heroicon::OutlinedClock, Sort: 1
    Labels: modelLabel = 'حضور وانصراف', pluralModelLabel = 'الحضور والانصراف', recordTitleAttribute = 'date'
    Pages: index (ListAttendances), create (CreateAttendance), edit (EditAttendance)
    Form Sections/Fields (AttendanceForm): Section 'تسجيل الحضور والانصراف': employee_id, date, check_in, check_out, status (AttendanceStatus enum), overtime_hours, notes.
    Table Columns (AttendancesTable): employee.name, date, check_in, check_out, status, overtime_hours, notes
    Table Actions: EditAction, DeleteAction
    Custom Actions: BulkAttendanceAction, BulkMarkWeekendAction
13. EmployeeAdvanceResource (Sort: 2)
    File: F:\Herd\Heaven\app\Filament\Resources\EmployeeAdvances\EmployeeAdvanceResource.php
    Model: App\Models\EmployeeAdvance
    Navigation: Group 'الموارد البشرية', Label 'سلف الموظفين', Icon: Heroicon::OutlinedBanknotes, Sort: 2
    Labels: modelLabel = 'سلفة موظف', pluralModelLabel = 'سلف الموظفين', recordTitleAttribute = 'code'
    Pages: index (ListEmployeeAdvances), create (CreateEmployeeAdvance), view (ViewEmployeeAdvance), edit (EditEmployeeAdvance)
    Global Search: code, notes
    Form Sections/Fields (EmployeeAdvanceForm): Section 'معلومات سلفة الموظف': code, employee_id, advance_date, amount, repaid_amount, installments, type (EmployeeAdvanceType enum), status (EmployeeAdvanceStatus enum), safe_id, journal_entry_id, notes.
    Table Columns (EmployeeAdvancesTable): code, employee.name, advance_date, amount, repaid_amount, installments, type, status, notes
    Table Actions: ViewAction, EditAction, DeleteAction
    Custom Actions: RecordRepaymentAction, PostAdvanceAction
14. PayrollRunResource (Sort: 3)
    File: F:\Herd\Heaven\app\Filament\Resources\PayrollRuns\PayrollRunResource.php
    Model: App\Models\PayrollRun
    Navigation: Group 'الموارد البشرية', Label 'مسيرات الرواتب', Icon: Heroicon::OutlinedReceiptPercent, Sort: 3
    Labels: modelLabel = 'مسير رواتب', pluralModelLabel = 'مسيرات الرواتب', recordTitleAttribute = 'code'
    Pages: index (ListPayrollRuns), create (CreatePayrollRun), view (ViewPayrollRun), edit (EditPayrollRun)
    Global Search: code, month, year
    Form Sections/Fields (PayrollRunForm): Section 'بيانات أساسية': code, fiscal_year_id, month, year, type (PayrollRunType enum). Section 'الإجماليات': total_gross, total_deductions, total_net. Section 'بيانات النظام': status (PayrollRunStatus enum), journal_entry_id.
    Table Columns (PayrollRunsTable): code, fiscalYear.name, month, year, type, total_gross, total_deductions, total_net, status
    Table Actions: ViewAction, EditAction, DeleteAction
    Relation Managers: PayrollLinesRelationManager
    Custom Actions: PostPayrollAction, MarkPayrollLinePaidAction, GenerateLinesAction, DuplicatePayrollRunAction, BulkMarkPaidAction, ApprovePayrollAction
    Widgets: PayrollRunsStatsWidget
15. EmployeeResource
    File: F:\Herd\Heaven\app\Filament\Resources\Employees\EmployeeResource.php
    Model: App\Models\Employee
    Navigation: Group 'الموارد البشرية', Label 'الموظفون', Icon: Heroicon::OutlinedUserGroup
    Labels: modelLabel = 'موظف', pluralModelLabel = 'الموظفون', recordTitleAttribute = 'name'
    Pages: index (ListEmployees), create (CreateEmployee), view (ViewEmployee), edit (EditEmployee), accounting-statement (EmployeeAccountingStatementPage)
    Global Search: code, name, national_id, phone, job_title
    Form Sections/Fields (EmployeeForm): Section 'معلومات الموظف': code, name, job_title, national_id, phone, email, address, department_id, type (EmployeeType enum), role, salary_type (SalaryType enum), daily_hours, base_salary, hire_date, termination_date, is_active, account_id, advance_account_id, user_id, notes.
    Table Columns (EmployeesTable): code, name, job_title, national_id, phone, email, department.name, type, salary_type, base_salary, is_active, hire_date
    Table Actions: DeactivateEmployeeAction, ViewAction, EditAction
    Relation Managers: PayrollLinesRelationManager, AttendancesRelationManager
    Custom Actions: DeactivateEmployeeAction, LinkUserAccountAction, MarkTodayAttendanceAction
16. ExpenseResource
    File: F:\Herd\Heaven\app\Filament\Resources\Expenses\ExpenseResource.php
    Model: App\Models\Expense
    Navigation: Group 'الموارد البشرية', Label 'المصروفات', Icon: Heroicon::OutlinedReceiptPercent
    Labels: modelLabel = 'مصروف', pluralModelLabel = 'المصروفات', recordTitleAttribute = 'description'
    Pages: index (ListExpenses), create (CreateExpense), view (ViewExpense), edit (EditExpense)
    Form Sections/Fields (ExpenseForm): Section 'بيانات المصروف': trip_id, category (office/marketing/transport/hotel_cost/airline_cost/other), description, amount. Section 'تفاصيل الدفع': payment_method (PaymentMethod enum), paid_at, paid_by. Section 'ملاحظات': notes.
    Table Columns (ExpensesTable): description, category, amount, payment_method, paid_at, trip.name, paidBy.name
    Table Actions: ViewAction, EditAction
    Custom Actions: ExportExpensesAction
    Widgets: ExpensesStatsWidget
17. DepartmentResource
    File: F:\Herd\Heaven\app\Filament\Resources\Departments\DepartmentResource.php
    Model: App\Models\Department
    Navigation: Group 'الموارد البشرية', Label 'الأقسام', Icon: Heroicon::OutlinedBuildingOffice
    Labels: modelLabel = 'قسم', pluralModelLabel = 'الأقسام', recordTitleAttribute = 'name'
    Pages: index (ListDepartments), create (CreateDepartment), view (ViewDepartment), edit (EditDepartment)
    Form Sections/Fields (DepartmentForm): Section 'بيانات القسم': name, parent_id, is_active.
    Table Columns (DepartmentsTable): name, parent.name, is_active
    Table Actions: ViewAction, EditAction
    Relation Managers: DepartmentChildrenRelationManager, EmployeesRelationManager
    Group 5: المحتوى (Content)
18. PostResource
    File: F:\Herd\Heaven\app\Filament\Resources\Posts\PostResource.php
    Model: App\Models\Post
    Navigation: Group 'المحتوى', Label 'الأخبار', Icon: Heroicon::OutlinedNewspaper
    Labels: modelLabel = 'خبر', pluralModelLabel = 'الأخبار', recordTitleAttribute = 'title'
    Pages: index (ListPosts), create (CreatePost), edit (EditPost)
    Form Sections/Fields (PostForm): image (FileUpload), title, slug, excerpt, content (RichEditor), published_at, is_published.
    Table Columns (PostsTable): image, title, excerpt, published_at, is_published
    Table Actions: ViewAction, EditAction
19. FaqResource
    File: F:\Herd\Heaven\app\Filament\Resources\Faqs\FaqResource.php
    Model: App\Models\Faq
    Navigation: Group 'المحتوى', Label 'الأسئلة الشائعة', Icon: Heroicon::OutlinedQuestionMarkCircle
    Labels: modelLabel = 'سؤال', pluralModelLabel = 'الأسئلة الشائعة', recordTitleAttribute = 'question'
    Pages: index (ListFaqs), create (CreateFaq), edit (EditFaq)
    Form Fields (FaqForm): question, answer, sort_order, is_published.
    Table Columns (FaqsTable): question, answer, sort_order, is_published
    Table Actions: ViewAction, EditAction
20. GalleryItemResource
    File: F:\Herd\Heaven\app\Filament\Resources\GalleryItems\GalleryItemResource.php
    Model: App\Models\GalleryItem
    Navigation: Group 'المحتوى', Label 'معرض الصور', Icon: Heroicon::OutlinedPhoto
    Labels: modelLabel = 'صورة', pluralModelLabel = 'معرض الصور', recordTitleAttribute = 'title'
    Pages: index (ListGalleryItems), create (CreateGalleryItem), edit (EditGalleryItem)
    Form Fields (GalleryItemForm): gallery (SpatieMediaLibraryFileUpload), title, caption, sort_order, is_published.
    Table Columns (GalleryItemsTable): gallery (SpatieMediaLibraryImageColumn), title, caption, sort_order, is_published
    Table Actions: ViewAction, EditAction
    Group 6: المحاسبة (Accounting)
21. AccountResource
    File: F:\Herd\Heaven\app\Filament\Resources\Accounts\AccountResource.php
    Model: App\Models\Account
    Navigation: Group 'المحاسبة', Label 'دليل الحسابات', Icon: Heroicon::OutlinedBookOpen
    Labels: modelLabel = 'حساب', pluralModelLabel = 'الحسابات', recordTitleAttribute = 'code'
    Pages: index (ListAccounts), create (CreateAccount), edit (EditAccount)
    Global Search: code, name, name_en
    Form Fields (AccountForm): code, name, name_en, class (AccountClass enum), type (AccountType enum), normal_balance (AccountNormalBalance enum), parent_id (relationship), level, is_active, is_system, notes.
    Table Columns (AccountsTable): code, name, name_en, class, type, normal_balance, parent.name, level, is_active, is_system, created_at
    Table Actions: ViewAction, EditAction
    Custom Actions: DeactivateAccountAction, ActivateAccountAction
22. FiscalYearResource
    File: F:\Herd\Heaven\app\Filament\Resources\FiscalYears\FiscalYearResource.php
    Model: App\Models\FiscalYear
    Navigation: Group 'المحاسبة', Label 'السنوات المالية', Icon: Heroicon::OutlinedCalendar
    Labels: modelLabel = 'سنة مالية', pluralModelLabel = 'السنوات المالية', recordTitleAttribute = 'name'
    Pages: index (ListFiscalYears), create (CreateFiscalYear), edit (EditFiscalYear), opening-balances (OpeningBalances)
    Global Search: name
    Form Sections/Fields (FiscalYearForm): Section 'بيانات السنة المالية': name, starts_at, ends_at, closed_at.
    Table Columns (FiscalYearsTable): name, starts_at, ends_at, status, closed_at, closedBy.name, created_at
    Table Actions: ViewAction, EditAction
    Relation Managers: JournalEntriesRelationManager, DocumentSequencesRelationManager
    Custom Actions: ReopenYearAction, CloseYearAction
23. JournalEntryResource
    File: F:\Herd\Heaven\app\Filament\Resources\JournalEntries\JournalEntryResource.php
    Model: App\Models\JournalEntry
    Navigation: Group 'المحاسبة', Label 'قيود اليومية', Icon: Heroicon::OutlinedDocumentText
    Labels: modelLabel = 'قيد يومية', pluralModelLabel = 'قيود اليومية', recordTitleAttribute = 'number'
    Pages: index (ListJournalEntries), create (CreateJournalEntry), edit (EditJournalEntry), trial-balance (TrialBalance), general-ledger (GeneralLedger)
    Global Search: number, description
    Form Sections/Fields (JournalEntryForm): Section 'بيانات القيد': number, fiscal_year_id, entry_date, source_type (JournalEntrySourceType enum), source_id, reference, posted_by. Section 'التفاصيل': description, notes. Section 'المرفقات': attachment.
    Table Columns (JournalEntriesTable): number, entry_date, fiscalYear.name, status, source_type, total_debits, total_credits, description, createdBy.name, posted_at
    Table Actions: PostEntryAction, ViewAction, EditAction
    Relation Managers: LinesRelationManager
    Custom Actions: ReverseEntryAction, PostEntryAction, DuplicateAsDraftAction
    Group 7: الخزينة والسندات (Treasury & Vouchers)
24. ReceiptVoucherResource (Sort: 0)
    File: F:\Herd\Heaven\app\Filament\Resources\ReceiptVouchers\ReceiptVoucherResource.php
    Model: App\Models\ReceiptVoucher
    Navigation: Group 'الخزينة والسندات', Label 'سندات القبض', Icon: Heroicon::OutlinedArrowDownOnSquare, Sort: 0
    Labels: modelLabel = 'سند قبض', pluralModelLabel = 'سندات القبض', recordTitleAttribute = 'number'
    Pages: index (ListReceiptVouchers), create (CreateReceiptVoucher), view (ViewReceiptVoucher), edit (EditReceiptVoucher)
    Global Search: number, description, payer_name
    Form Sections/Fields (ReceiptVoucherForm): Section 'بيانات السند': number, voucher_date, receipt_method (VoucherPaymentMethod enum), safe_id (conditional), bank_account_id (conditional), cheque_number, cheque_date. Section 'الدافع': payer_type (PayerType enum), client_id, booking_id, payment_type (PaymentType enum), supplier_id, employee_id, payer_name. Section 'المبلغ': amount. Section 'إضافات': description, reference, attachment.
    Table Columns (ReceiptVouchersTable): number, voucher_date, booking.reference, payment_type, amount, payer_type, status, safe.name, bankAccount.bank_name, description
    Table Actions: PostVoucherAction, ViewAction, EditAction
    Custom Actions: PostVoucherAction
25. PaymentVoucherResource (Sort: 1)
    File: F:\Herd\Heaven\app\Filament\Resources\PaymentVouchers\PaymentVoucherResource.php
    Model: App\Models\PaymentVoucher
    Navigation: Group 'الخزينة والسندات', Label 'سندات الصرف', Icon: Heroicon::OutlinedArrowUpOnSquare, Sort: 1
    Labels: modelLabel = 'سند صرف', pluralModelLabel = 'سندات الصرف', recordTitleAttribute = 'number'
    Pages: index (ListPaymentVouchers), create (CreatePaymentVoucher), view (ViewPaymentVoucher), edit (EditPaymentVoucher)
    Global Search: number, description, payee_name
    Form Sections/Fields (PaymentVoucherForm): Section 'بيانات السند': number, voucher_date, payment_method (VoucherPaymentMethod enum), safe_id, bank_account_id, cheque_number, cheque_date. Section 'المستلم': payee_type (PayeeType enum -- supplier/client/employee/branch/agent/other), contextual selects for each type, payee_name. Section 'المبالغ': amount, withholding_amount, net_amount (auto-calculated). Section 'إضافات': description, reference, attachment.
    Table Columns (PaymentVouchersTable): number, voucher_date, amount, net_amount, payee_type, status, safe.name, bankAccount.bank_name, description
    Table Actions: PostVoucherAction, ViewAction, EditAction
    Custom Actions: PostVoucherAction
26. RefundVoucherResource (Sort: 2)
    File: F:\Herd\Heaven\app\Filament\Resources\RefundVouchers\RefundVoucherResource.php
    Model: App\Models\RefundVoucher
    Navigation: Group 'الخزينة والسندات', Label 'سندات الاسترداد', Icon: Heroicon::OutlinedArrowUturnLeft, Sort: 2
    Labels: modelLabel = 'سند استرداد', pluralModelLabel = 'سندات الاسترداد', recordTitleAttribute = 'number'
    Pages: index (ListRefundVouchers), create (CreateRefundVoucher), view (ViewRefundVoucher), edit (EditRefundVoucher)
    Global Search: number, description, reference
    Form Sections/Fields (RefundVoucherForm): Section 'بيانات السند': number, voucher_date, payment_method (VoucherPaymentMethod enum), safe_id, bank_account_id, cheque_number, cheque_date. Section 'الطرف': party_type (RefundPartyType enum -- client/supplier), client_id, supplier_id, booking_id. Section 'المبلغ': amount, reference. Section 'إضافات': description, attachment.
    Table Columns (RefundVouchersTable): number, voucher_date, party_type, party_name (computed), amount, payment_method, safe.name, bankAccount.bank_name, booking.reference, status, description
    Table Actions: PostVoucherAction, ViewAction, EditAction
    Custom Actions: PostVoucherAction
27. SafeResource (Sort: 3)
    File: F:\Herd\Heaven\app\Filament\Resources\Safes\SafeResource.php
    Model: App\Models\Safe
    Navigation: Group 'الخزينة والسندات', Label 'الخزائن', Icon: Heroicon::OutlinedBanknotes, Sort: 3
    Labels: modelLabel = 'خزينة', pluralModelLabel = 'الخزائن', recordTitleAttribute = 'code'
    Pages: index (ListSafes), create (CreateSafe), edit (EditSafe)
    Global Search: code, name
    Form Fields (SafeForm): code, name, account_id (chart-of-accounts link), responsible_employee_id, is_active, notes.
    Table Columns (SafesTable): code, name, account.name, responsibleEmployee.name, is_active, created_at
    Table Actions: ViewAction, EditAction
28. BankAccountResource (Sort: 4)
    File: F:\Herd\Heaven\app\Filament\Resources\BankAccounts\BankAccountResource.php
    Model: App\Models\BankAccount
    Navigation: Group 'الخزينة والسندات', Label 'الحسابات البنكية', Icon: Heroicon::OutlinedBuildingOffice2, Sort: 4
    Labels: modelLabel = 'حساب بنكي', pluralModelLabel = 'الحسابات البنكية', recordTitleAttribute = 'code'
    Pages: index (ListBankAccounts), create (CreateBankAccount), edit (EditBankAccount)
    Global Search: code, bank_name, account_number, iban
    Form Fields (BankAccountForm): code, bank_name, branch, account_number, iban, account_id (chart-of-accounts link, filtered to code like '1233%'), is_active, notes.
    Table Columns (BankAccountsTable): code, bank_name, branch, account_number, iban, account.name, is_active, created_at
    Table Actions: ViewAction, EditAction
    Group 8: التقارير المالية (Financial Reports)
    No dedicated Filament resources found in this group. The reports are likely implemented as custom Filament pages or widgets (e.g., TrialBalance and GeneralLedger pages under JournalEntryResource, accounting statement pages under Client/Employee/Supplier resources).
    Custom Pages (app/Filament/Pages/)
    Reports (Group: التقارير المالية - Financial Reports)
    Page Label (Arabic) Icon Sort Custom View
    IncomeStatement قائمة الدخل heroicon-o-document-chart-bar 1 filament.pages.reports.income-statement
    BalanceSheet الميزانية العمومية heroicon-o-scale 2 filament.pages.reports.balance-sheet
    CashFlowStatement قائمة التدفقات النقدية heroicon-o-currency-dollar 3 filament.pages.reports.cash-flow-statement
    ExpenseSummaryReport تقرير المصروفات heroicon-o-arrow-trending-down 5 filament.pages.reports.expense-summary-report
    PackageProfitabilityReport ربحية الباقات heroicon-o-chart-pie 6 filament.pages.reports.package-profitability-report
    ClientAgingReport أعمار ديون العملاء heroicon-o-clock 7 filament.pages.reports.aging-report
    SupplierBalancesReport أرصدة الموردين heroicon-m-scale 8 filament.pages.reports.aging-report
    Each has PDF export via PdfService. All use HasReportFilters trait for fiscal year/date filtering.

Group 9: الإعدادات (Settings)

1. PackageTypeResource
   File: F:\Herd\Heaven\app\Filament\Resources\PackageTypes\PackageTypeResource.php
   Model: App\Models\PackageType
   Navigation: Group 'الإعدادات', Label 'أنواع الباقات', Icon: Heroicon::OutlinedTag
   Labels: modelLabel = 'نوع باقة', pluralModelLabel = 'أنواع باقات', recordTitleAttribute = 'name_ar'
   Pages: index (ListPackageTypes), create (CreatePackageType), edit (EditPackageType)
   Form Sections/Fields (PackageTypeForm): Section 'بيانات النوع': name, name_ar, slug (auto-generated), color, icon, is_religious. Section 'مدة الرحلة': duration_nights_min, duration_nights_max.
   Table Columns (PackageTypesTable): name_ar, color, is_religious, duration_nights_min, duration_nights_max, packages_count
2. CityResource
   File: F:\Herd\Heaven\app\Filament\Resources\Cities\CityResource.php
   Model: App\Models\City
   Navigation: Group 'الإعدادات', Label 'المدن', Icon: Heroicon::OutlinedMapPin
   Labels: modelLabel = 'مدينة', pluralModelLabel = 'المدن', recordTitleAttribute = 'name_ar'
   Pages: index (ListCities), create (CreateCity), edit (EditCity)
   Form Sections/Fields (CityForm): Section 'بيانات المدينة': name, name_ar, country.
   Table Columns (CitiesTable): name_ar, name, country, hotels_count
   Additional Plugin-Nav Items (also in الإعدادات group)
   From AdminPanelProvider.php, the following plugin items are registered in 'الإعدادات':

Item Navigation Label Icon Sort
Backups Backups-نسخ احتياطى (FilamentSpatieLaravelBackup) heroicon-o-cpu-chip 3
Log Viewer Log-Viewer (FilamentLogViewer) heroicon-o-document-text 10
Summary Statistics
Metric Count
Total Resources 30 resource directories, 30 Resource classes
Navigation Groups 9 (8 with resources, 1 for reports as custom pages)
Relation Managers 20 across the app
Custom Actions ~35+ custom Action classes
Widgets 8 widget classes
Custom Pages 10+ custom pages (statements, trial balance, general ledger, rooming/manifest dashboards, opening balances)
