# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## [3.0.0] - Unreleased
### Summary
This release now requires PHP 8

Breaking changes:
- Trying to validate or evaluate an InputObject before the value was set will now throw an `Error` instead of a `BadMethodCallException`.
  If you encounter a property access before initialization error, this is the cause, and it indicates incorrect use of the library.

## [2.2.0] - 2021-07-19
### Summary
This release focuses on supporting newer versions of PHP and minimizing external dependencies.

This will probably be the last version in the 2.x line.
The next major version will only support PHP 8, and should further improve type information that's available in modern tooling.

### Added
- Test suite: PHP 7.4, 8.0
- Type information has been added in numerous places.

### Changed (Internal)
- Migrated from Travis to Github Actions
- Migrated from Coveralls to Codecov
- Tests are no longer exported. Test traits for client code have been moved to `src` so they will still be available in releases.
- Code style updated to PSR-12

### Removed
- Test suite: PHP 7.1

## [2.1.5] - 2019-02-04
### Summary
- More tests changes to improve compatibility with PHPUnit 8

## [2.1.4] - 2019-02-04
### Summary
- Changes some tests to improve compatibility with PHPUnit 8

## [2.1.3] - 2019-01-16
### Summary
- Fixes some erroneously defined PHPDoc type signatures
- Adds PHPStan to CI
- Stops testing against PHP 7.0, which is no longer maintained

## [2.1.2] - 2018-09-20
### Summary
- Fixes an issue where default values were erroneously returned where `null` was a valid value

## [2.1.1] - 2018-05-16
### Summary
- Fixes regression in 2.1.0 that created a breaking change without an appropriate SemVer bump

## [2.1.0] - 2018-03-18
### Summary
- Configures CI
- Adds support for default values on optional parameters
- Exposes additional information about validation errors

## [2.0.0] - 2015-10-21
### Summary
- PHP 7 Support
  - Adds return types
  - Adds scalar type hints

## [1.0.0] - 2015-10-21
### Summary
- First stable release (no major changes)

## [0.0.4: Add SafeInputTestTrait] - 2015-10-08
### Summary
- Adds SafeInputTestTrait

## [0.0.3] - 2015-09-19
### Summary
- Changes InputException to extend UnexpectedValueException

## [0.0.2] - 2015-09-17
### Summary
- Add ValidationInterface test trait
- Moved InputObjects to a separate repository

## [0.0.1] - 2015-09-01
### Summary
- Initial release
