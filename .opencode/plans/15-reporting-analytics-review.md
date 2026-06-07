# Reporting & Analytics Review

## Scope
Revenue reports, visa dashboards, occupancy reports, client statements, and all data aggregation logic.

## Areas to Cover
- `app/Services/ReportService.php`
- `app/Filament/Pages/ReportsPage.php`
- Widget queries in `app/Filament/Widgets/`
- PDF export of reports (revenue, visa, occupancy)

## Review Prompts
1. **Revenue accuracy**: Does revenue reporting correctly aggregate payments by type (deposit/installment/final) across date ranges?
2. **Query optimization**: Are report queries optimized for large datasets? Any N+1 or full table scans?
3. **Date filtering**: Are date range filters applied consistently across all report sections?
4. **Visa dashboard accuracy**: Does the visa status breakdown match actual data?
5. **Occupancy reporting**: Is seat occupancy calculated correctly across all packages and trips?
6. **Client statement**: Does the statement show all payments, refunds, and outstanding balances correctly?
7. **Currency formatting**: Are all monetary values formatted in EGP with proper number formatting?
8. **PDF export completeness**: Do PDF exports include all the same data as the screen reports?
9. **Filter persistence**: Do report filters persist across page visits?
10. **Missing reports**: Are there any reports the business needs that aren't implemented?
