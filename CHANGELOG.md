# Release Notes for Teamleader

## 5.2.0 - unreleased
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
