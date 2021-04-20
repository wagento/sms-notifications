# Changelog for Wagento SMS Notifications

All notable changes to this extension will be documented in this file.

The format is based on [Keep a Changelog], and this project adheres to
[Semantic Versioning].

For more information about this extension, please see the [README] document.

## [Unreleased]

## [1.1.1] - 2021-04-20

### Fixed

- Resolved error when store name is not available while parsing message variables.

## [1.1.0] - 2021-03-24

### Changed

- Updated dependencies on core Magento modules to include versions from Magento
  2.4
- Refactored install and uninstall scripts to Declarative Schema configuration
and Data Patches
- Refactored templates to use a stand-alone Escaper object for output
  
### Removed
- Removed compatibility logic for Magento 2.2

## [1.0.2] - 2019-10-01

### Changed

- The extension now supports PHP versions greater than 7.2

## [1.0.1] - 2019-08-26

### Added
- API configuration fields can now be exported to `app/etc/env.php` along with
flags for enabling/disabling the extension and debug mode when using the
`php bin/magento app:config:dump` command

### Changed
- Refactored the "SMS Notification Type" fields on the "My Text Notifications"
page to submit in one array together instead of submitting individually
- Moved user roles to more appropriate locations (management roles are now under
"Customers" and settings role is now under "Stores > Settings > Configuration")
- The logo displayed on the configuration page in the backend has been updated

### Fixed
- Removed dashes from the example phone number in the comment for the "Source"
API Configuration field (dashes cause the LINK Mobility API to return an
"Invalid source number" error)
- The "Mobile Telephone Number" field on the "My Text Notifications" page is no
longer required if no SMS Notification Types are selected
- The "My Text Notifications" page now returns a "Page Not Found" (404) error if
the extension is disabled in the configuration

## [1.0.0] - 2019-04-24

### Added
- Initial version of integration between Magento 2 and LINK Mobility SMS service.
- Support for sending mobile text notifications when an order is placed,
shipped, cancelled, refunded, held, or released from hold

[Unreleased]: https://github.com/wagento/sms-notifications/compare/1.1.1...HEAD
[1.1.1]: https://github.com/wagento/sms-notifications/compare/1.1.0...1.1.1
[1.1.0]: https://github.com/wagento/sms-notifications/compare/1.0.2...1.1.0
[1.0.2]: https://github.com/wagento/sms-notifications/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/wagento/sms-notifications/compare/1.0.0...1.0.1
[1.0.0]: https://github.com/wagento/sms-notifications/releases/tag/1.0.0
[Keep a Changelog]: https://keepachangelog.com/en/1.0.0/
[Semantic Versioning]: https://semver.org/spec/v2.0.0.html
[README]: ./README.md