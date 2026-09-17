# Changelog

## [v2.1.0] - 2026-09-17

- Added: `ImageData::mimeType()` and `ImageData::fileSize()`
- Fix: PHPStan errors

## [v2.0.0] - 2026-09-04

- Changed (BREAKING): drop support for Acorn 4 / Laravel 10-11, require Acorn 5 / Laravel 12 only

## [v1.6.0] - 2026-09-02

- Added: support for Acorn 5 / Laravel 12 (Acorn 4 / Laravel 10 & 11 still supported)
- Changed: bump league/commonmark from 2.9.0 to 2.10.0

## [v1.5.2] - 2026-08-18

- Fixed: `url()` method now checks post type visibility
- Changed: dependency bumps (spatie/laravel-data, symfony/routing, symfony/mailer, symfony/yaml, symfony/http-foundation, league/commonmark)

## [v1.5.1] - 2026-05-05

- Fixed: check for zero value before retrieving objects
- Changed: bump spatie/laravel-data

## [v1.5.0] - 2026-05-01

- Added: `CommentData`
- Changed: bump spatie/laravel-data, league/commonmark

## [v1.4.0] - 2026-03-19

- Added: allow `Carbon` as a meta type
- Changed: bump spatie/laravel-data, league/commonmark

## [v1.3.3] - 2026-02-11

- Fixed: don't show `url()` for non-public posts
- Changed: update to PHP 8.2; dependency bumps (spatie/laravel-data, psy/psysh, setup-php action)

## [v1.3.2] - 2025-11-24

- Fixed: term data creation
- Changed: bump actions/checkout

## [v1.3.1] - 2025-11-17

- Fixed: inheritance issue
- Changed: dependency bumps (spatie/laravel-data, symfony/http-foundation, setup-php action)

## [v1.3.0] - 2025-09-01

- Added: `TermData`
- Changed: dependency bumps (setup-php, actions/checkout)

## [v1.2.0] - 2025-07-08

- Added: utility functions for hierarchical post types
- Changed: bump spatie/laravel-data

## [v1.1.1] - 2025-06-23

- Added: allow backed enums as an `enum` meta type
- Changed: bump spatie/laravel-data, setup-php action

## [v1.1.0] - 2025-05-26

- Added: post type `supports` mapping to a data class for custom post types
- Fixed: add `url()` method to the `PostData` interface

## [v1.0.8] - 2025-03-10

- Added: post status label
- Changed: dependency bumps (php-cs-fixer, setup-php action, nesbot/carbon, league/commonmark, spatie/laravel-data)

## [v1.0.7] - 2024-12-06

- Added: optional excerpt count
- Fixed: add post status `inherit`
- Changed: allow PHP >= 8.1; use Yard organization workflows; add dependabot auto-merge

## [v1.0.6] - 2024-12-02

- Changed: `PostData::dataClass()` now returns `static` instead of `self` by default
- Changed: dependency bumps (php-cs-fixer, markdownlint-cli2-action)

## [v1.0.5] - 2024-10-29

- Changed: minor composer.json update

## [v1.0.4] - 2024-10-15

- Fixed: terms prefix
- Changed: dependency bumps (composer group)

## [v1.0.3] - 2024-08-13

- Changed: dependency bumps (composer group)

## [v1.0.2] - 2024-07-19

- Added: test suite (Pest), PHPStan (levels 5-9), CI workflows (tests, PHPStan, badges, markdown lint, composer lock diff, php-cs-fixer), dependabot config
- Changed: refactored `PostData::url()` method; consistent class imports
- Fixed: readme linting

## [v1.0.1] - 2024-07-15

- Added: support for snake_case meta keys; cast meta value to data object
- Docs: readme heading consistency

## [v1.0.0] - 2024-06-11

- Initial release of the Yard Data package
