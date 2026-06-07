# Enums, Types & Configuration Review

## Scope
All 12 PHP enums for completeness, consistency, and integration with Filament. Also covers app configuration.

## Areas to Cover
- `app/Enums/` — All 12 enums (BookingStatus, PackageType, PackageGrade, TripStatus, PaymentMethod, PaymentType, RoomType, VisaStatus, Gender, MaritalStatus, SupplierType, SalaryType)
- `app/Concerns/` — PasswordValidationRules, ProfileValidationRules
- `config/` — Key configuration files

## Review Prompts
1. **Enum completeness**: Are all necessary values represented in each enum? Are any missing (e.g., PaymentMethod missing "credit_card")?
2. **Filament integration**: Do all enums implement HasColor/HasIcon/HasLabel? Are labels and colors context-appropriate?
3. **Arabic labels**: Are all enum labels translated to Arabic? Are there any inconsistencies?
4. **Missing enums**: Are there any hardcoded strings that should be enums (e.g., employee roles, notification types)?
5. **Validation trait completeness**: Do PasswordValidationRules and ProfileValidationRules cover all edge cases?
6. **Config review**: Are app settings (timezone, locale, currency) correct and consistently referenced?
7. **Default values**: Do enum defaults match expected business defaults?
