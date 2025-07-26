# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v1.0.0.html).

## [1.2.0] - 2025-01-27
### Added
- Automated release workflows for version management and deployment
- Version bump workflow for conventional commits and changelog updates
- Release workflow for automated GitHub releases with assets
- Support for conventional commit format in CI/CD pipeline
- Comprehensive badges for project status and workflows
- Professional release documentation and process guides

### Changed
- Enhanced CI/CD pipeline with automated release management
- Improved changelog generation and maintenance process
- Updated README with comprehensive release process documentation

## [Unreleased]
- Ongoing improvements and maintenance.

## [1.0.0] - 2025-07-26
### Added
- Comprehensive test coverage (79 tests, 0 failures, 0 risky tests)
- Full support for Laravel 11 and 12
- GitHub Actions CI for PHP 8.3/8.4 and Laravel 11/12
- Code coverage reporting via Codecov with Xdebug enabled
- Code style checks with PHP CS Fixer
- Static analysis with Larastan across all PHP/Laravel combinations
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

## [0.0.1] - 2023-xx-xx
- Initial release with basic like/dislike functionality, events, and artisan command support.

[Unreleased]: https://github.com/turahe/laravel-likeable/compare/v1.2.0...HEAD
[1.2.0]: https://github.com/turahe/laravel-likeable/compare/v1.0.0...v1.2.0
[1.0.0]: https://github.com/turahe/laravel-likeable/releases/tag/v1.0.0 