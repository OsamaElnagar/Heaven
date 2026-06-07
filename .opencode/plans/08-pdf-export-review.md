# PDF Generation & Export Review

## Scope
All 10 PDF templates, generation logic, data accuracy, and formatting. Covers the `laravel-mpdf` integration and all export actions.

## Areas to Cover
- `resources/views/pdf/` — All 10 PDF Blade templates
- PDF generation calls in Filament resources and ReportService

## PDF Templates
1. Revenue Report
2. Visa List
3. Payment Receipt
4. Booking Voucher
5. Trip Manifest
6. Rooming List
7. Package Summary
8. Client Statement
9. Client Card
10. Expense List

## Review Prompts
1. **Data accuracy**: Does each PDF template render the correct data without gaps or miscalculations?
2. **Arabic rendering**: Does `mpdf` render Arabic text correctly with proper RTL and font support?
3. **Formatting**: Are numbers (currency, dates, booking references) properly formatted?
4. **Edge cases**: What happens when a booking has no payments (null paid_amount)? Empty rooming list?
5. **Performance**: Are large PDFs (e.g., manifests for 300+ pilgrims) timing out or consuming too much memory?
6. **Consistency**: Do all exports use consistent branding (logo, colors, headers/footers)?
7. **File naming**: Are exported PDFs named consistently and meaningfully?
8. **Download vs stream**: Are PDFs served as downloads or inline? Is the content-disposition header correct?
