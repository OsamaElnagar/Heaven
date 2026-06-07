# Authentication & Security Review

## Scope
All authentication flows (Fortify), 2FA, password management, email verification, rate limiting, and security settings.

## Areas to Cover
- `config/fortify.php` — Fortify configuration
- `app/Actions/Fortify/` — CreateNewUser, ResetUserPassword, UpdateUserProfileInformation
- `app/Providers/FortifyServiceProvider.php`
- `routes/settings.php` — Profile, appearance, security routes
- `resources/views/pages/auth/` — All 7 auth Blade views
- `resources/views/pages/settings/` — Profile, appearance, security settings
- `tests/Feature/Auth/` — All 6 auth test files

## Review Prompts
1. **Rate limiting**: Login and 2FA have 5 req/min — is this too restrictive for a real business? Should it be higher?
2. **2FA flow**: Is the 2FA challenge working correctly after password confirmation? Is the recovery code flow tested?
3. **Email verification**: Are unverified users properly restricted from verified-only routes (dashboard, security settings)?
4. **Password rules**: Are password requirements (length, complexity) aligned with security best practices?
5. **Account deletion**: Does profile deletion properly cascade to related data (bookings, payments)?
6. **Session management**: Are users properly logged out on password change? Are other devices' sessions invalidated?
7. **Registration validation**: Does CreateNewUser properly validate all fields? Are there any missing validations?
8. **CSRF**: Are all forms properly protected with CSRF tokens?
9. **Redirect after login**: Is the redirect path correct for different user roles?
10. **Test coverage**: Are the existing auth tests comprehensive? Are there missing edge cases (e.g., expired reset links)?
