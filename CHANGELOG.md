# Release Notes for Teamleader

## 5.2.1 - 2026-04-02
### Fixed
- Fixed country field only accepting labels (e.g., "Belgium") — now also accepts ISO codes (e.g., "BE") from prefilled dropdowns, with case-insensitive matching. ([#8](https://github.com/craftpulse/craft-teamleader-focus/issues/8)) - Thanks [@ishetnogferre](https://github.com/ishetnogferre)
- Fixed tags field only accepting arrays — now also normalizes comma-, semicolon-, and pipe-separated strings from hidden fields. ([#10](https://github.com/craftpulse/craft-teamleader-focus/issues/10)) - Thanks [@ishetnogferre](https://github.com/ishetnogferre)
- Fixed empty values in tag arrays not being filtered out, matching the string path behavior.
- Fixed `VatHelper::formatVatNumber()` stripping letters from non-Belgian EU VAT numbers (FR, NL, IE, ES, etc.) and applying Belgian-specific dot formatting. Now validates against per-country EU patterns and outputs raw alphanumeric format as expected by the Teamleader Focus API.
- Fixed `IdentityProviderException` being thrown with an empty error message in OAuth client `checkResponse`.

### Changed
- Removed unused `$options` parameter from `_prepPayload()` and dead `$options` variable in `sendPayload()`.
- Added explicit `private` visibility to OAuth/API URL constants in auth client (PHP 8.2).
- Removed unused static `$plugin` property from main plugin class — use inherited `::getInstance()` instead.

## 5.2.0 - 2026-01-18
### Added
- Added "Tags" integration field for Contacts
- Added "Tags" integration field for Companies
- Added "Append Tags" option for Contacts to preserve existing tags during updates (uses `contacts.tag` endpoint)
- Added "Append Tags" option for Companies to preserve existing tags during updates (uses `companies.tag` endpoint)
- Added "Remarks" integration field for Contacts
- Added "Remarks" integration field for Companies
- Added "Summary" integration field for Deals
- Added `fax` field mapping for both contacts and companies.
- Added `currency` field mapping for deals - allows mapping currency from a form field.
- Added `Default Currency` setting for deals - configurable fallback when currency is not mapped from a form field.
- Added `CurrencyHelper` class for formatting currency options from Teamleader Focus API.
- Added `Client Type` custom Formie field for B2B/B2C workflow differentiation.
- B2B (Company) requests create contact + company + deal with linking.
- B2C (Client) requests create contact + deal only, skipping company creation.
- Field displays as radio buttons with configurable labels and default value.
- Added `ClientTypeHelper` for detecting client type from form submissions.

### Changed
- Renamed Deals "Extra Information" field to "Summary" with correct API handle

### Fixed
- Fixed Deals field using incorrect API handle `remarks` instead of `summary`
- Fixed `linkToCompany` toggle having no effect - contacts are now properly linked to companies via the `contacts.linkToCompany` API endpoint when both entities exist and the setting is enabled.
- Removed `mobile_phone` field from companies mapping - Teamleader Focus API only supports `phone` and `fax` for companies.

## 5.1.1 - 2026-01-15
### Fixed
- Fixed custom fields not being sent in the correct API format (now properly structured as `custom_fields` array)
- Fixed `contact_person_id` sending empty string instead of being omitted when not applicable
- Fixed `contact_person_id` now only included when customer is a company and a contact person exists
- Fixed mobile phone type using `'phone'` instead of `'mobile'` for the telephone type
- Removed `context` from API payload (internal use only, not an API field)

## 5.1.0 - 2026-01-13
### Added
- Added "Remarks" integration field for creating remarks via forms
- Added "estimated_value" integration field for pushing amounts to Teamleader Focus

### Changed
- Refactored VAT number formatting into a reusable helper function
- Made email on companies optional to match Teamleader Focus API Specs

### Fixed
- Fixed context filters not properly limiting API field results
- Fixed custom fields not saving correctly to Teamleader Focus
- Fixed address generation not conforming to Teamleader Focus API specs
- Fixed mobile_phone mapping incorrectly unsetting `phone` instead of `mobile_phone`
- Fixed PHPStan return type in VatHelper::formatVatNumber()

## 5.0.2 - 2025-03-10
### Fixed
- Fixed the path of the icon-mask to `teamleader`, using an alias looks to the namespace, not the folder structure or plugin handle

## 5.0.1 - 2025-02-26
### Fixed
- Fixed the template path of the settings templates to `teamleader-focus` as the plugin had to be renamed

## 5.0.0 - 2025-02-24
- Initial Release
