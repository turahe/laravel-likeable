# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
- Ongoing improvements and maintenance.

## [2.0.0] - 2024-07-26
### Added
- Comprehensive test coverage (79 tests, 0 failures, 0 risky tests)
- Full support for Laravel 10, 11, and 12
- GitHub Actions CI for PHP 8.3/8.4 and Laravel 10/11/12
- Code coverage reporting via Codecov
- Security scanning and code style checks
- New tests for all major features: likes, dislikes, toggles, events, exceptions, console commands, and service layer
- Detailed README with usage, CI, and testing instructions

### Changed
- Refactored counter management for reliability (moved logic from observers to service methods)
- Improved event dispatching and test isolation
- Updated LikeType enum handling for PHP 8.3/8.4 compatibility
- Improved test database setup and teardown
- Enhanced error handling and test reliability

### Fixed
- Fixed all previously failing and risky tests
- Fixed unique constraint and counter bugs in toggle and service logic
- Fixed console command output and test expectations
- Fixed morph map and model alias issues in tests

## [1.0.0] - 2023-xx-xx
- Initial release with basic like/dislike functionality, events, and artisan command support.

[Unreleased]: https://github.com/turahe/laravel-likeable/compare/v2.0.0...HEAD
[2.0.0]: https://github.com/turahe/laravel-likeable/releases/tag/v2.0.0 