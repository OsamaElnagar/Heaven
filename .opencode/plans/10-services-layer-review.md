# Services Layer & Business Logic Review

## Scope
All 5 service classes for code quality, error handling, testing, and completeness of business logic.

## Areas to Cover
- `app/Services/BookingService.php`
- `app/Services/PaymentService.php`
- `app/Services/TripService.php`
- `app/Services/VisaService.php`
- `app/Services/ReportService.php`

## Review Prompts
1. **Error handling**: Do all services throw meaningful exceptions on failure? Are exceptions caught and handled at the controller/Filament level?
2. **Validation**: Are business rules validated before execution (e.g., can't book a full package)?
3. **Transactions**: Are operations that touch multiple models wrapped in database transactions?
4. **Testability**: Are services using dependency injection? Can they be easily mocked in tests?
5. **Return types**: Do all methods have proper return type hints and consistent return shapes?
6. **Duplication**: Is there duplicated logic between services? Could any be shared?
7. **ReportService**: Are revenue/vaccancy/client-statement queries optimized? N+1 risks?
8. **Missing features**: Are there any business operations that should be in a service but are handled ad-hoc?
9. **Side effects**: Do service methods have unintended side effects on related models?
