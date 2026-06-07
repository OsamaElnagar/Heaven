# Testing Coverage Review

## Scope
Test coverage gaps, missing test suites, and test quality. This review should identify untested critical paths and suggest test additions.

## Areas to Cover
- `tests/Feature/` — Auth tests, Settings tests, DashboardTest
- `tests/Unit/` — ExampleTest
- `tests/Pest.php` — Test configuration
- All service classes, observers, enums, and Filament resources (for testability assessment)

## Review Prompts
1. **Core business logic**: Are BookingService, PaymentService, TripService, VisaService, and ReportService covered by tests? If not, what tests should be added?
2. **Observer testing**: Are all 4 observers tested for their side effects?
3. **Filament resources**: Are any Filament resources tested (actions, forms, tables)?
4. **Enum tests**: Are enums tested for correct labels and transitions?
5. **Edge case coverage**: Are there tests for boundary conditions (oversold packages, invalid status transitions, empty reports)?
6. **RefreshDatabase**: Pest.php has RefreshDatabase commented out — is this intentional? Does it cause test pollution?
7. **Integration tests**: Are there tests that verify the full booking→payment→visa→trip workflow?
8. **Factory quality**: Do factories produce valid, realistic data?
9. **Performance tests**: Are there any performance or load tests for critical queries?
10. **Test speed**: Are tests optimized to run quickly, or is there slow test overhead?
